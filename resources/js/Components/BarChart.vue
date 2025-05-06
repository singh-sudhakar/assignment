<template>
    <div class="text-right">
      <!-- Dropdown for switching views -->
      <Dropdown>
        <template #trigger>
          <button class="px-4 py-2 bg-blue-600 text-white rounded">
            {{ selectedLabel }}
            <i class="fas fa-chevron-down ml-2"></i>
          </button>
        </template>

        <template #content>
          <button @click="setView('weekly')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i class="fas fa-calendar-week mr-2"></i> Weekly
          </button>
          <button @click="setView('monthly')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            <i class="fas fa-calendar-alt mr-2"></i> Monthly
          </button>
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
  import Dropdown from '@/Components/Dropdown.vue'

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
    const groupColors = {
      A: '#3B82F6',
      B: '#10B981',
      C: '#F59E0B',
      D: '#EF4444'
    }

    const datasets = groups.map(group => ({
      label: `Group ${group}`,
      data: labels.map(label => {
        const match = dataset.find(d =>
          d.group === group &&
          (isWeekly ? `Week ${d.week}` === label : `Month ${d.month}` === label)
        )
        return match ? match.total : 0
      }),
      backgroundColor: groupColors[group]
    }))

    // Flatten dataset values to compute max value
    const allValues = datasets.flatMap(d => d.data)
    const maxY = Math.max(...allValues) + 2

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
        scales: {
          y: {
            beginAtZero: true,
            suggestedMax: maxY,
            ticks: {
              stepSize: 1
            },
            title: {
              display: true,
              text: 'Patient Count'
            }
          }
        },
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: `${selectedLabel.value} Patient Group Distribution`
          }
        }
      }
    })
  }

  onMounted(renderChart)
  </script>
