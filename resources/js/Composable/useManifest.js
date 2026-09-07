import { computed } from "vue";

export function useManifest(manifest) {
    const PAYMENT_STATUS_PENDING = 0;
    const PAYMENT_STATUS_PAID = 1;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_ONPROGRESS = 2;
    const STATUS_AMEND = 3;
    const STATUS_REJECTED = -1;

    const isPaymentStatusPaid = computed(() => {
        return manifest.payment_status == PAYMENT_STATUS_PAID;
    });

    const isPaymentStatusPending = computed(() => {
        return manifest.payment_status == PAYMENT_STATUS_PENDING;
    });

    const isStatusNotInitiated = computed(() => {
        return manifest.payment_status == PAYMENT_STATUS_PENDING;
    });

    const isStatusPending = computed(() => {
        return (
            manifest.payment_status == PAYMENT_STATUS_PAID &&
            manifest.status == STATUS_PENDING
        );
    });

    const isStatusApproved = computed(() => {
        return (
            manifest.payment_status == PAYMENT_STATUS_PAID &&
            manifest.status == STATUS_APPROVED
        );
    });

    const isStatusRejected = computed(() => {
        return (
            manifest.status == STATUS_REJECTED
        );
    });

    const isStatusAmend = computed(() => {
        return (
            manifest.status == STATUS_AMEND
        );
    });

    const isStatusOnProgress = computed(() => {
        return (
            manifest.status == STATUS_ONPROGRESS
        );
    });

    const primaryPassengers = computed(() => {
        return manifest.passengers.filter((item) => {
            return !item.is_additional;
        })
    })

    const additionalPassengers = computed(() => {
        return manifest.passengers.filter((item) => {
            return item.is_additional == 1;
        })
    })

    return {
        isPaymentStatusPaid,
        isPaymentStatusPending,
        isStatusNotInitiated,
        isStatusPending,
        isStatusApproved,
        isStatusOnProgress,
        isStatusRejected,
        isStatusAmend,
        primaryPassengers,
        additionalPassengers
    };
}
