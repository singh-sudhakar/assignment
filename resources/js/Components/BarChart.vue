<template>
    <div class="text-right">
      <!-- Dropdown for switching views -->
      <Dropdown>
        <template #trigger>
          <button class="px-4 py-2 bg-blue-600 text-white rounded">
            {{ selectedLabel }}
          </button>
        </template>

        <template #content>
          <button @click="setView('weekly')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Weekly</button>
          <button @click="setView('monthly')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Monthly</button>
        </template>
      </Dropdown>

      <!-- Bar Chart -->
      <canvas id="barChart" class="mt-6"></canvas>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import Chart from 'chart.js/auto'
  import Dropdown from '@/Components/Dropdown.vue' // Adjust path as needed

  const selectedView = ref('weekly')
  const selectedLabel = ref('Weekly')
  let chartInstance = null

  const setView = (view) => {
    selectedView.value = view
    selectedLabel.value = view === 'weekly' ? 'Weekly' : 'Monthly'
    renderChart()
  }

  const renderChart = async () => {
    const res = await axios.get('/api/chart/bar', { withCredentials: true })
    const isWeekly = selectedView.value === 'weekly'
    const dataset = isWeekly ? res.data.weekly : res.data.monthly
    const labels = [...new Set(dataset.map(item => isWeekly ? `Week ${item.week}` : `Month ${item.month}`))]

    const groups = ['A', 'B', 'C', 'D']
    const datasets = groups.map(group => ({
      label: `Group ${group}`,
      data: labels.map(label => {
        const match = dataset.find(d =>
          d.group === group &&
          (isWeekly ? `Week ${d.week}` === label : `Month ${d.month}` === label)
        )
        return match ? match.total : 0
      }),
      backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    }))

    if (chartInstance) chartInstance.destroy()

    const ctx = document.getElementById('barChart')
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: `${selectedLabel.value} Patient Group Distribution` }
        }
      }
    })
  }

  onMounted(renderChart)
  </script>
