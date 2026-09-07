<script setup>
defineProps({
    fees: { type: Array, required: true },
    amountFormatted: { type: String, required: true },
});

function formatCurrency(amount) {
    return (
        "RM " +
        Number(amount ?? 0)
            .toFixed(2)
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    );
}

function getFeeLines(fee) {
    const lines = [];
    if (fee.local_adult > 0)
        lines.push(
            `Local Adult: ${fee.local_adult} × ${formatCurrency(fee.local_adult_fee)}`,
        );
    if (fee.local_child > 0)
        lines.push(
            `Local Child: ${fee.local_child} × ${formatCurrency(fee.local_child_fee)}`,
        );
    if (fee.foreign_adult > 0)
        lines.push(
            `Foreign Adult: ${fee.foreign_adult} × ${formatCurrency(fee.foreign_adult_fee)}`,
        );
    if (fee.foreign_child > 0)
        lines.push(
            `Foreign Child: ${fee.foreign_child} × ${formatCurrency(fee.foreign_child_fee)}`,
        );
    if (fee.boat_fee > 0)
        lines.push(`Boat Fee: ${formatCurrency(fee.boat_fee)}`);
    return lines;
}
</script>

<template>
    <div class="p-3 bg-light border-top border-bottom">
        <p class="fw-bold text-primary mb-2">
            <span
                class="material-icons align-middle me-1"
                style="font-size: 16px"
                >account_tree</span
            >
            Affected Manifests &amp; Fee Breakdown
        </p>

        <em v-if="fees.length === 0" class="text-muted small">
            No manifest fee records linked to this payment.
        </em>

        <div v-else class="table-responsive">
            <table
                class="table table-sm table-bordered table-striped bg-white mb-0"
            >
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 44px">View</th>
                        <th>Form No.</th>
                        <th>Departure Date</th>
                        <th>Company</th>
                        <th>Boat No.</th>
                        <th>Destination</th>
                        <th class="text-center">Type</th>
                        <th>Fee Breakdown</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="fee in fees" :key="fee.manifest_fee_id">
                        <td class="text-center align-middle">
                            <a
                                v-if="fee.show_url"
                                :href="fee.show_url"
                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center p-0"
                                style="width: 28px; height: 28px"
                                @click.stop
                                title="View Manifest Detail"
                            >
                                <span
                                    class="material-icons"
                                    style="font-size: 14px"
                                    >open_in_new</span
                                >
                            </a>
                        </td>
                        <td class="align-middle fw-semibold text-primary">
                            {{ fee.form_number }}
                        </td>
                        <td class="align-middle">{{ fee.departure_date }}</td>
                        <td class="align-middle">{{ fee.company_name }}</td>
                        <td class="align-middle">{{ fee.boat_number }}</td>
                        <td class="align-middle">{{ fee.destination }}</td>
                        <td class="text-center align-middle">
                            <span
                                class="badge"
                                :class="
                                    fee.type === 'Additional'
                                        ? 'bg-warning text-dark'
                                        : 'bg-info text-dark'
                                "
                                >{{ fee.type }}</span
                            >
                        </td>
                        <td class="align-middle">
                            <div
                                v-for="(line, i) in getFeeLines(fee)"
                                :key="i"
                                class="small text-muted"
                            >
                                {{ line }}
                            </div>
                        </td>
                        <td
                            class="text-end align-middle fw-semibold text-success"
                        >
                            {{ formatCurrency(fee.total) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="8" class="text-end fw-bold">
                            Transaction Total
                        </td>
                        <td class="text-end fw-bold text-primary">
                            {{ amountFormatted }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>
