<template>
    <Head title="Patients" />
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patients</h2>
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div v-if="successMessage" class="text-green-500 p-2 mb-4 border bg-green-100">
              {{ successMessage }}
            </div>

            <!-- Grid Layout for Search and Add Button -->
            <div class="grid grid-cols-5 gap-4 m-6">
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
                <div class="col-span-1 text-right">
                    <PrimaryButton @click="openAddModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Add Patient
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
                    <PrimaryButton @click="updatePatient(patient)" class="bg-blue-500 hover:bg-blue-600  text-white px-4 py-1 rounded">
                        Update
                    </PrimaryButton>
                    <PrimaryButton @click="deletePatient(patient.id)" class="bg-red-500 hover:bg-red-600  text-white ml-3 px-2 py-1 rounded">
                        Delete
                    </PrimaryButton>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="mt-4 flex justify-between items-center">
              <PrimaryButton
                :disabled="currentPage === 1"
                @click="fetchPatients(currentPage - 1)"
                class="bg-gray-500 text-white px-4 py-2 rounded"
              >
                Previous
              </PrimaryButton>
              <span>Page {{ currentPage }} of {{ totalPages }}</span>
              <PrimaryButton
                :disabled="currentPage === totalPages"
                @click="fetchPatients(currentPage + 1)"
                class="bg-gray-500 text-white px-4 py-2 rounded"
              >
                Next
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
