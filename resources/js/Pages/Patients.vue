<template>
    <Head title="Patients" />
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patients</h2>
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div v-if="successMessage" class="text-green-500 p-2 mb-4 border bg-green-100">
              {{ successMessage }}
            </div>

            <!-- Grid Layout for Search and Add Button -->
            <div class="grid grid-cols-5 mb-4">
                <!-- Search Field (4/5 of the row) -->
                <div class="col-span-4">
                    <input
                        v-model="searchQuery"
                        @keyup="fetchPatients()"
                        type="text"
                        placeholder="Search patient..."
                        class="w-full p-2 border rounded"
                        />
                </div>

                <!-- Add Patient Button (1/5 of the row) -->
                <div class="col-span-1 text-right mt-2">
                    <PrimaryButton @click="openAddModal" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded cursor-pointer">
                        <i class="fas fa-user-plus"></i> Add Patient
                    </PrimaryButton>
                </div>
            </div>


            <!-- Patient Table -->
            <table class="w-full border-collapse border">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border p-2 text-left">Name</th>
                  <th class="border p-2">Group</th>
                  <th class="border p-2">Wait Time (Mins)</th>
                  <th class="border p-2">Consultation Date</th>
                  <th class="border p-2">Week</th>
                  <th class="border p-2">Month</th>
                  <th class="border p-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="patient in patients" :key="patient.patient_id">
                  <td class="border p-2">{{ patient.name }}</td>
                  <td class="border p-2 text-center">{{ patient.group }}</td>
                  <td class="border p-2 text-center">{{ patient.wait_time }}</td>
                  <td class="border p-2 text-center">{{ patient.consultation_date }}</td>
                  <td class="border p-2 text-center">{{ patient.week }}</td>
                  <td class="border p-2 text-center">{{ patient.month }}</td>
                  <td class="border p-2 text-center">
                    <PrimaryButton @click="updatePatient(patient)" class="bg-indigo-500 hover:bg-indigo-600  text-white px-4 py-1 rounded cursor-pointer">
                        <i class="fas fa-edit"></i>
                    </PrimaryButton>
                    <PrimaryButton @click="deletePatient(patient.id)" class="bg-red-500 hover:bg-red-600  text-white ml-3 px-2 py-1 rounded cursor-pointer">
                        <i class="fas fa-trash-alt"></i>
                    </PrimaryButton>
                  </td>
                </tr>
              </tbody>
            </table>

          <!-- Pagination Controls -->
            <div class="mt-10 flex justify-center items-center space-x-6">
                <!-- Previous Button -->
                <PrimaryButton
                    :disabled="currentPage === 1"
                    @click="fetchPatients(currentPage - 1)"
                    :class="[
                    'text-white px-3 py-2 rounded cursor-pointer',
                    currentPage === 1
                        ? 'bg-gray-400 cursor-not-allowed pointer-events-none'
                        : 'bg-indigo-500 hover:bg-indigo-600'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </PrimaryButton>

                <!-- Page Info -->
                <span class="text-sm text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>

                <!-- Next Button -->
                <PrimaryButton
                    :disabled="currentPage === totalPages"
                    @click="fetchPatients(currentPage + 1)"
                    :class="[
                    'text-white px-3 py-2 rounded cursor-pointer',
                    currentPage === totalPages
                        ? 'bg-gray-400 cursor-not-allowed pointer-events-none'
                        : 'bg-indigo-500 hover:bg-indigo-600'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </PrimaryButton>
            </div>


          </div>
        </div>
      </div>
    </AuthenticatedLayout>

    <!-- Add/Edit Patient Modal -->
    <AddPatientModal
      v-if="showAddPatientModal"
      :form="form"
      :mode="editingPatient ? 'edit' : 'add'"
      @add-patient="savePatient"
      @update-patient="savePatient"
      @close="showAddPatientModal = false"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="showDeletePatientModal"
      ref="confirmationModal"
      :patientId="patientToDelete"
      @confirm="confirmDeletePatient"
      @cancel="showDeletePatientModal = false"
    />
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import ConfirmationModal from '@/Components/ConfirmationModal.vue';
  import AddPatientModal from '@/Components/AddPatientModal.vue';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';

  const patients = ref([]);
  const form = ref({
    name: '',
    group: 'A',
    wait_time: '',
    consultation_date: ''
  });
  const successMessage = ref('');
  const patientToDelete = ref(null);

  const showAddPatientModal = ref(false);
  const showDeletePatientModal = ref(false);
  const editingPatient = ref(null);
  const searchQuery = ref('');

  const currentPage = ref(1);
  const totalPages = ref(1);
  const perPage = 10;

  const fetchPatients = async (page = 1) => {
    try {
      const res = await axios.get(`/api/patients`, {
        params: {
          search: searchQuery.value,
          page,
          perPage,
        },
        withCredentials: true,
      });
      patients.value = res.data.data;
      totalPages.value = res.data.last_page;
      currentPage.value = page;
    } catch (error) {
      console.error('Error fetching patients:', error);
    }
  };

  const openAddModal = () => {
    form.value = {
      name: '',
      group: 'A',
      wait_time: '',
      consultation_date: ''
    };
    editingPatient.value = null;
    showAddPatientModal.value = true;
  };

  const updatePatient = (patient) => {
    form.value = { ...patient };
    editingPatient.value = patient;
    showAddPatientModal.value = true;
  };

  const savePatient = async (data) => {
    try {
      await axios.get('/sanctum/csrf-cookie');
      if (editingPatient.value) {
        await axios.put(`/api/patients/${editingPatient.value.id}`, data, { withCredentials: true });
        successMessage.value = 'Patient updated successfully!';
      } else {
        await axios.post('/api/patients', data, { withCredentials: true });
        successMessage.value = 'Patient added successfully!';
      }
      fetchPatients();
      showAddPatientModal.value = false;
    } catch (error) {
      console.error('Error saving patient:', error);
    }
  };

  const deletePatient = (id) => {
    patientToDelete.value = id;
    showDeletePatientModal.value = true;
  };

  const confirmDeletePatient = async (id) => {
    try {
      await axios.get('/sanctum/csrf-cookie');
      await axios.delete(`/api/patients/${id}`, { withCredentials: true });
      successMessage.value = 'Patient successfully deleted!';
      fetchPatients();
      showDeletePatientModal.value = false;
    } catch (error) {
      console.error('Error deleting patient:', error);
    }
  };

  onMounted(() => {
    fetchPatients();
  });
  </script>
