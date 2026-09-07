import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useRoleStore = defineStore('role', () => {
  const role = ref(null)

  function getRole() {
    return role.value;
  }

  function setRole(rolevalue) {
    role.value = rolevalue;
  }

  return {
    role,
    getRole,
    setRole,
  }
})
