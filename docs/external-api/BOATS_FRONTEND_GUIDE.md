# Boats & Crew — Frontend Integration Guide

How to wire the Manifest Form's boat/crew sections to `GET /api/external/boats` and
`GET /api/external/boatmen`:

- **Boat & Company Information** — Boat No. / Boatman Name + IC No. / Assistant
  Boatman Name + IC No. → `/boats` (boat-scoped crew, cascading from the selected boat).
- **Instructor / Divemaster / Guide Details** — no Boat No. field, because this crew
  isn't assigned to any specific boat → `/boatmen` (flat, all types, all companies).

This is a practical, copy-pasteable companion to [`openapi.yaml`](openapi.yaml) — see
that file for the full formal contract, and [`README.md`](README.md) for how to test
against a running app.

## Why two endpoints

`Boatman` records come in 5 types, but only types 1 (Boatman) and 2 (Assistant) are
ever assigned to a boat (`boat_id` set) — that's what the Boat & Company Information
section's cascading dropdowns need. Types 3 (Instructor), 4 (Divemaster), and 5
(Guide) are company-wide crew with `boat_id` always `null`, which is exactly why that
part of the form has no Boat No. selector at all — there's nothing to cascade from.

| Endpoint            | Shape                                   | Use for                                                  |
| -------------------- | ---------------------------------------- | ---------------------------------------------------------- |
| `GET /api/external/boats`    | boats, each with nested `boatman[]` (types 1/2 only) | Boat No. → Boatman Name / Assistant Boatman Name cascade |
| `GET /api/external/boatmen`  | flat list, all 5 types, all companies, each with its own `company` | Instructor / Divemaster / Guide dropdowns (types 3/4/5) |

Both are unparameterized `GET`s, no pagination — fetch each once (e.g. on form load)
and keep the results in memory, same as `/destinations`, `/activities`, and
`/nationalities`.

```
Authorization: Bearer <EXTERNAL_API_TOKEN>
Accept: application/json
```

## 1. Boat No. → Boatman / Assistant Boatman

### Response shape → UI field mapping

```json
{
  "success": true,
  "message": "List Boats",
  "data": [
    {
      "id": 1,
      "name": "Sea Explorer",
      "number": "SA/P11/0513",
      "license": "LIC-00512",
      "license_expiry_date": "2026-08-01",
      "capacity": 12,
      "company": { "id": 3, "name": "Coral Breeze Resort Semporna" },
      "boatman": [
        {
          "id": 10,
          "boat_id": 1,
          "name": "Ahmad bin Yusof",
          "ic_no": "901231-12-1234",
          "mate_card": "MC-1029",
          "seaman_card_no": "SC-5521",
          "type": 1,
          "type_label": "Boatman"
        },
        {
          "id": 11,
          "boat_id": 1,
          "name": "Rahim bin Osman",
          "ic_no": "920501-12-5678",
          "mate_card": null,
          "seaman_card_no": "SC-5522",
          "type": 2,
          "type_label": "Assistant"
        }
      ]
    }
  ]
}
```

| Form field                     | Source                                                                 |
| -------------------------------- | ------------------------------------------------------------------------- |
| Boat No. (dropdown)              | `data[].number` (option value should be `data[].id`)                     |
| Boatman Name (dropdown)          | `name` of entries in the selected boat's `boatman` where `type === 1`    |
| Boatman IC No. (auto-filled)     | `ic_no` of the selected boatman                                          |
| Assistant Boatman Name           | `name` of entries in the selected boat's `boatman` where `type === 2`    |
| Assistant IC No. (auto-filled)   | `ic_no` of the selected assistant boatman                                |

A boat's `boatman` array will only ever contain types 1 and 2 — instructors,
divemasters, and guides never appear here (see §2). A boat with no crew returns
`"boatman": []` — handle that as an empty/disabled state, not an error.

### Implementation walkthrough (vanilla JS)

```js
const API_BASE = 'https://<partner-domain>/api/external';
const API_TOKEN = '<EXTERNAL_API_TOKEN>'; // keep server-side / out of client bundles if possible

let boats = []; // cached after the first fetch

async function loadBoats() {
  const res = await fetch(`${API_BASE}/boats`, {
    headers: { Authorization: `Bearer ${API_TOKEN}`, Accept: 'application/json' },
  });

  if (res.status === 401) throw new Error('Unauthorized — check the partner bearer token.');
  if (!res.ok) throw new Error(`Failed to load boats (HTTP ${res.status})`);

  const body = await res.json(); // { success, message, data }
  boats = body.data;
  populateBoatDropdown(boats);
}

function populateBoatDropdown(boats) {
  const boatSelect = document.getElementById('boat_no');
  boatSelect.innerHTML = '<option value="">Please Select Option</option>';
  for (const boat of boats) {
    const opt = document.createElement('option');
    opt.value = boat.id;
    opt.textContent = boat.number;
    boatSelect.appendChild(opt);
  }
}

// Call this on the Boat No. dropdown's change event.
function onBoatSelected(boatId) {
  const boat = boats.find((b) => b.id === Number(boatId));

  const boatmanMain = boat ? boat.boatman.filter((b) => b.type === 1) : [];
  const boatmanAsst = boat ? boat.boatman.filter((b) => b.type === 2) : [];

  populateCrewDropdown('boatman_name', 'boatman_ic_no', boatmanMain);
  populateCrewDropdown('assistant_boatman_name', 'assistant_ic_no', boatmanAsst);
}

// Shared by both this section and the Instructor/Divemaster/Guide section below.
function populateCrewDropdown(selectId, icFieldId, crew) {
  const select = document.getElementById(selectId);
  const icField = document.getElementById(icFieldId);

  select.innerHTML = '<option value="">Please Select Option</option>';
  for (const person of crew) {
    const opt = document.createElement('option');
    opt.value = person.id;
    opt.textContent = person.name;
    opt.dataset.icNo = person.ic_no ?? '';
    select.appendChild(opt);
  }

  icField.value = '';
  select.onchange = () => {
    const selected = select.selectedOptions[0];
    icField.value = selected?.dataset.icNo || '';
  };
}
```

Wire-up:

```js
document.getElementById('boat_no').addEventListener('change', (e) => onBoatSelected(e.target.value));
loadBoats().catch((err) => console.error(err));
```

## 2. Instructor / Divemaster / Guide

No Boat No. context here — these dropdowns list a company's instructors, divemasters,
and guides regardless of which boat (if any) is being used, matching the "Add
Instructor" / "Add Divemaster" / "Add Guide" (multi-add) sections of the form.

### Response shape → UI field mapping

```json
{
  "success": true,
  "message": "List Boatmen",
  "data": [
    {
      "id": 20,
      "boat_id": null,
      "name": "Faizal Instructor",
      "ic_no": "880101-12-1111",
      "mate_card": null,
      "seaman_card_no": null,
      "type": 3,
      "type_label": "Instructor",
      "company": { "id": 3, "name": "Coral Breeze Resort Semporna" }
    }
  ]
}
```

| Form field                        | Source                                                  |
| ------------------------------------ | ---------------------------------------------------------- |
| Instructor Name (dropdown)           | `name` of entries where `type === 3`                       |
| Instructor No./IC No./Passport No.   | `ic_no` of the selected instructor                          |
| Divemaster Name (dropdown)           | `name` of entries where `type === 4`                       |
| Divemaster No./IC No./Passport No.   | `ic_no` of the selected divemaster                          |
| Guide Name (dropdown)                | `name` of entries where `type === 5`                        |
| Guide No./IC No./Passport No.        | `ic_no` of the selected guide                                |

`data` here mixes all 5 types (types 1/2 are included too, each with `boat_id` set) —
always filter by `type` client-side rather than assuming the response only contains
crew relevant to this section.

### Implementation walkthrough (vanilla JS)

Reuses `populateCrewDropdown` from §1 — same shape (`id`, `name`, `ic_no`), just from
a flat list instead of a boat's nested array:

```js
async function loadBoatmen() {
  const res = await fetch(`${API_BASE}/boatmen`, {
    headers: { Authorization: `Bearer ${API_TOKEN}`, Accept: 'application/json' },
  });

  if (res.status === 401) throw new Error('Unauthorized — check the partner bearer token.');
  if (!res.ok) throw new Error(`Failed to load boatmen (HTTP ${res.status})`);

  const body = await res.json();
  const boatmen = body.data;

  populateCrewDropdown('instructor_name', 'instructor_ic_no', boatmen.filter((b) => b.type === 3));
  populateCrewDropdown('divemaster_name', 'divemaster_ic_no', boatmen.filter((b) => b.type === 4));
  populateCrewDropdown('guide_name', 'guide_ic_no', boatmen.filter((b) => b.type === 5));
}
```

If the form supports adding multiple instructors/divemasters/guides ("+ Add
Instructor" etc.), render one `<select>` + IC field row per add, all populated from
the same cached `boatmen` array — no need to re-fetch per row.

Wire-up: call `loadBoatmen()` alongside `loadBoats()` on form load.

```js
Promise.all([loadBoats(), loadBoatmen()]).catch((err) => console.error(err));
```

## Quick manual test

```bash
curl -H "Authorization: Bearer <EXTERNAL_API_TOKEN>" -H "Accept: application/json" \
     https://<partner-domain>/api/external/boats

curl -H "Authorization: Bearer <EXTERNAL_API_TOKEN>" -H "Accept: application/json" \
     https://<partner-domain>/api/external/boatmen
```

Or use Postman: import [`postman_collection.json`](postman_collection.json) and run
the **List Boats** / **List Boatmen** requests — same auth wired up as the other
requests in the collection (see [`README.md`](README.md) for setting the
`external_api_token` variable), plus saved `200`/`401` example responses to compare
against.
