# API Filtering & Sorting — Frontend Guide

Every list endpoint takes its filters and sort order from the query string, in the
same format, via [Spatie Laravel Query Builder](https://spatie.be/docs/laravel-query-builder):

```
GET <endpoint>?filter[<field>]=<value>&sort=[-]<field>
```

- `filter[field]=value` — narrow the list. Several filters combine with **AND**.
- Comma-separated value = **OR** within that one filter: `filter[type]=3,4,5`.
- `sort=field` ascending, `sort=-field` descending. Several: `sort=-age,name`.
- Only the fields listed below are accepted. Anything else is rejected with
  **HTTP 400** (see [Errors](#errors)) — it is never silently ignored.
- An empty value (`filter[name]=`) is ignored, same as leaving the filter out.
- Remember to URL-encode values (`encodeURIComponent`); `[` `]` in the key may stay as is.

Two filter kinds show up in the tables:

| Kind        | Behaviour                                                             |
| ----------- | --------------------------------------------------------------------- |
| **exact**   | Whole value must match (`filter[gender]=F`). Comma list = any of.     |
| **partial** | Case-insensitive "contains" (`filter[title]=mat` matches "Mataking"). |

A comma always splits the value into a list, so a partial filter with a comma in it
searches for *each* piece (`filter[name]=Smith,Jones` = Smith **or** Jones).

---

## 1. External partner API — `/api/external/*`

Auth: `Authorization: Bearer <EXTERNAL_API_TOKEN>`. Full contract:
[`external-api/openapi.yaml`](external-api/openapi.yaml), live at `/docs/external-api`.

| Endpoint                | Filters                                                                                                                | Sorts (default first)            |
| ----------------------- | ---------------------------------------------------------------------------------------------------------------------- | -------------------------------- |
| `GET /destinations`     | `id` exact · `code` exact · `title` partial                                                                            | `title`, `id`, `code`            |
| `GET /activities`       | `id` exact · `code` exact · `title` partial                                                                            | `title`, `id`, `code`            |
| `GET /nationalities`    | `id` exact · `code` exact · `title` partial                                                                            | `title`, `id`, `code`            |
| `GET /boats`            | `id` exact · `company_id` exact · `number` partial                                                                     | `number`, `id`                   |
| `GET /boatmen`          | `id` · `boat_id` · `company_id` · `type` · `ic_no` (all exact) · `name` partial                                        | `name`, `id`, `type`             |
| `GET /guests` (paged)   | `id` · `ic_no` · `gender` (exact) · `name` · `nationality_name` (partial) · `last_synced_at` (see below)               | `id`, `name`, `age`              |

Use a `-` prefix on any sort for descending (`sort=-title`).

### Examples

```
# Typeahead for a destination dropdown
GET /api/external/destinations?filter[title]=mat&sort=title

# Only the boats of one resort, matching a typed boat number
GET /api/external/boats?filter[company_id]=3&filter[number]=P11

# Instructor / Divemaster / Guide dropdowns (crew types 3, 4, 5), in one call
GET /api/external/boatmen?filter[type]=3,4,5&sort=name

# Crew of one boat
GET /api/external/boatmen?filter[boat_id]=1

# Guests: females named Siti, oldest first, page 2
GET /api/external/guests?filter[name]=siti&filter[gender]=F&sort=-age&limit=50&page=2
```

Crew types: `1` Boatman · `2` Assistant · `3` Instructor · `4` Divemaster · `5` Guide.

### Guests: pagination and incremental sync

`/guests` is the only paginated list: `page` (default 1) and `limit` (default 1000, max 2000).
Response carries `meta: { current_page, per_page, total, last_page }`.

`filter[last_synced_at]=<ISO 8601 timestamp>` returns only guests updated **more than
15 minutes after** that timestamp (exactly 15 minutes is excluded). Omit it for a first full
sync, and keep the same value on every page of one sync. Other filters combine with it.

### Response shape

```json
{ "success": true, "message": "List Boats", "data": [ ... ] }
```

Filtering never changes the item shape — only which items come back. No match is
`200` with `"data": []`, not a 404.

---

## 2. Mobile API — `GET /api/activity`

Auth: Sanctum bearer token. The list is already scoped to what the logged-in user may
see (an agent sees only their company's manifests, an authority only paid + final ones
for their jetty, …); filters narrow *within* that scope.

| Filter                  | Kind            | Meaning                                                                          |
| ----------------------- | --------------- | -------------------------------------------------------------------------------- |
| `search`                | partial         | Free text over **company name, boat number and form number**                     |
| `status`                | exact           | Approval status. `0` Pending · `1` Approved · `2` In Progress · `3` Amend · `-1` Rejected. Comma list allowed: `status=0,3` |
| `payment_status`        | exact           | `0` Pending · `1` Paid · `-1` Failed. Comma list allowed                          |
| `departure_date`        | exact date      | `YYYY-MM-DD`                                                                     |
| `departure_date_from`   | date, inclusive | `YYYY-MM-DD` — on or after                                                       |
| `departure_date_to`     | date, inclusive | `YYYY-MM-DD` — on or before                                                      |

**Sorts**: `departure_date` (default, `-departure_date` i.e. newest first), `created_at`,
`updated_at`, `form_number`, `company_name`, `boat_number`, `status`, `payment_status`.

Other parameters: `page` and `per_page` (default 20).

### Examples

```
# Search box
GET /api/activity?filter[search]=coral

# "Needs my attention" tab
GET /api/activity?filter[status]=0,3

# Calendar range, soonest first
GET /api/activity?filter[departure_date_from]=2026-09-01&filter[departure_date_to]=2026-09-30&sort=departure_date

# Combined, page 2
GET /api/activity?filter[search]=P11&filter[status]=1&sort=-updated_at&page=2&per_page=20
```

### Response

```json
{
  "message": "List Manifest Activity",
  "data": {
    "list": [ { "id": 1, "form_number": "FRM-001", "departure_date": "2026-09-01", "status": 1, "...": "..." } ],
    "meta": {
      "current_page": 1, "last_page": 3, "per_page": 20, "total": 47,
      "min_date": "2026-01-05", "max_date": "2026-09-20"
    },
    "filters": { "filter": { "status": "1" } }
  }
}
```

`meta.min_date` / `meta.max_date` are the earliest and latest departure date **that the
current `search` / `status` / `payment_status` filters leave available** — the `departure_date*`
filters themselves are deliberately not applied to them, so a date picker keeps its full range
while a date is selected. `filters` echoes the accepted input.

### Legacy parameters (existing mobile app builds)

Still supported, translated into the filters above — new code should use `filter[...]`:

| Legacy                                                | Same as                                         |
| ----------------------------------------------------- | ----------------------------------------------- |
| `search_fields[]=search&search_values[]=coral`        | `filter[search]=coral`                          |
| `search_fields[]=status&search_values[]=1`            | `filter[status]=1`                              |
| `search_fields[]=departure_date&search_values[]=…`    | `filter[departure_date]=…`                      |
| `order_type=asc` / `desc`                             | `sort=departure_date` / `sort=-departure_date`  |

If both forms are sent for the same field, `filter[...]` wins. `order_by` was never usable
(every value failed validation) and is now ignored.

---

## Errors

| Status | When                                            | Body                                                                                      |
| ------ | ----------------------------------------------- | ----------------------------------------------------------------------------------------- |
| `400`  | Unknown filter name or unknown sort field       | The `message` names the bad value and lists the allowed ones (below)                      |
| `422`  | Malformed value (bad date, non-numeric status…) | `{ "message": "...", "errors": { "filter.departure_date": ["..."] } }`                    |
| `401`  | Missing / invalid token                         | —                                                                                         |

`400` body — external API: `{ "success": false, "message": "Requested filter(s) `bogus` are not allowed. Allowed filter(s) are `id, code, title`.", "data": null }`;
mobile API: the same `message` without the `success`/`data` envelope.

Because a typo is an error rather than a silent no-op, a frontend can rely on "200 means
the filter was applied".

## Building the query string

```js
// Skips empty values, joins arrays with commas, encodes values.
function listQuery({ filter = {}, sort, ...rest } = {}) {
  const p = new URLSearchParams();
  for (const [k, v] of Object.entries(filter)) {
    const value = Array.isArray(v) ? v.join(',') : v;
    if (value !== '' && value != null) p.append(`filter[${k}]`, value);
  }
  if (sort) p.append('sort', sort);
  for (const [k, v] of Object.entries(rest)) if (v != null) p.append(k, v);
  return p.toString(); // brackets come out as %5B %5D — Laravel decodes them
}

fetch(`/api/activity?${listQuery({ filter: { status: [0, 3], search }, sort: '-updated_at', page })}`);
```

## For backend developers: adding a filter

1. Add it to `allowedFilters([...])` in the controller (external API) or
   `GetManifestActivity::queryBuilder()` (mobile). Use `AllowedFilter::exact` / `partial`,
   or `AllowedFilter::callback` for custom logic. Add sortable columns to `allowedSorts`.
2. Mobile only: add a validation rule for it in `ActivitySearchRequest`.
3. External API: update `docs/external-api/openapi.yaml` (and the Postman collection) —
   see the repo `CLAUDE.md`.
4. Update the tables in this guide and add a case to
   `tests/Feature/Api/External/FilterSortTest.php` or `tests/Feature/Api/ActivityListFilterTest.php`.
