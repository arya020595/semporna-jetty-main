<script setup>
const props = defineProps({
    passengers: Array,
});
</script>
<template>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Guest Name</th>
                    <th scope="col">IC/Passport No.</th>
                    <th scope="col">Nationality</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Activity</th>
                    <th scope="col">Next of Kin</th>
                    <th scope="col">Emergency No</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!props.passengers.length">
                    <td colspan="8">
                        <strong>There is no data!</strong>
                    </td>
                </tr>
                <tr v-for="(passenger, index) in props.passengers">
                    <td>{{ index + 1 }}</td>
                    <td>{{ passenger.name }}</td>
                    <td>{{ passenger.ic_no }}</td>
                    <td>{{ passenger.nationality_name }}</td>
                    <td>{{ passenger.gender_text }}</td>
                    <td>{{ passenger.activity_name }}</td>
                    <td>{{ passenger.next_of_kin }}</td>
                    <td>{{ passenger.emergency_contact }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
