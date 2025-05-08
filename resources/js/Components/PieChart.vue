<template>
    <canvas id="pieChart"></canvas>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import Chart from 'chart.js/auto'
  import ChartDataLabels from 'chartjs-plugin-datalabels'
  import axios from 'axios'

  // Register plugin
  Chart.register(ChartDataLabels)

  onMounted(async () => {
    const res = await axios.get('/api/chart/pie', { withCredentials: true })

    const labels = res.data.map(item => item.group)
    const data = res.data.map(item => item.total)

    const ctx = document.getElementById('pieChart').getContext('2d')
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          data,
          backgroundColor: ['#4A90E2', '#50E3C2', '#F5A623', '#9013FE']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          datalabels: {
            color: '#000',
            font: {
              weight: 'bold',
              size: 13
            },
            offset: 10,
            formatter: (value, context) => {
              const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0)
              const percentage = ((value / total) * 100).toFixed(1)
              return `${percentage}%`
            }
          }
        }
      },
      plugins: [ChartDataLabels]
    })
  })
  </script>
