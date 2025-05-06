<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  form: Object,
  mode: {
    type: String,
    default: 'add'
  }
})

const emit = defineEmits(['add-patient', 'update-patient', 'close'])

const localForm = ref({ ...props.form })

watch(
  () => props.form,
  (newForm) => {
    localForm.value = { ...newForm }
  },
  { immediate: true }
)

const handleSubmit = () => {
  if (props.mode === 'edit') {
    emit('update-patient', localForm.value)
  } else {
    emit('add-patient', localForm.value)
  }
  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">{{ props.mode === 'edit' ? 'Update Patient' : 'Add Patient' }}</h2>
      <div class="mb-4">
        <label class="block text-gray-700">Name:</label>
        <input v-model="localForm.name" class="w-full p-2 border rounded" type="text" />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Group:</label>
        <select v-model="localForm.group" class="w-full p-2 border rounded">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Wait Time(Mins):</label>
        <input v-model="localForm.wait_time" class="w-full p-2 border rounded" type="text" />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Consultation Date:</label>
        <input v-model="localForm.consultation_date" class="w-full p-2 border rounded" type="date" />
      </div>
      <div class="flex justify-end gap-2">
        <PrimaryButton @click="emit('close')" class="bg-gray-500 hover:bg-gray-600  text-white px-4 py-1 cursor-pointer rounded">Cancel</PrimaryButton>
        <PrimaryButton @click="handleSubmit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 cursor-pointer rounded">
          {{ props.mode === 'edit' ? 'Update' : 'Add' }} Patient
        </PrimaryButton>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
