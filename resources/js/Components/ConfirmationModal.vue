<script setup>
import { defineProps, defineEmits, ref } from 'vue';

const props = defineProps({
  patientId: Number
});

const emit = defineEmits(['confirm', 'cancel']);

const isVisible = ref(true); // Modal visibility

const confirm = () => {
  emit('confirm', props.patientId);
  isVisible.value = false; // Close the modal after confirming
};

const cancel = () => {
  emit('cancel');
  isVisible.value = false; // Close the modal after canceling
};
</script>

<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content">
  <p>Are you sure you want to delete this patient?</p>
  <PrimaryButton class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-1 mt-2 mr-2 rounded cursor-pointer" @click="confirm">
    Confirm
  </PrimaryButton>
  <PrimaryButton class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 mt-2 rounded cursor-pointer" @click="cancel">
    Cancel
  </PrimaryButton>
</div>

  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 400px;
  text-align: center;
}

button {
  margin: 10px;
  padding: 5px 10px;
    border-radius: 5px;
}
.confirm {
  background-color: #4CAF50;
  color: white;
}

.cancel {
  background-color: #f44336;
  color: white;
}
</style>
