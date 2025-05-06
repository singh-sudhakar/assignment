<template>
    <canvas id="pieChart"></canvas>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import Chart from 'chart.js/auto'
  import axios from 'axios'

  onMounted(async () => {
    const res = await axios.get('/api/chart/pie', {
      withCredentials: true,
    })

    const labels = res.data.map(item => item.group)
    const data = res.data.map(item => item.total)

    new Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          label: 'Patient Groups',
          data,
          backgroundColor: [
            '#4A90E2',
            '#50E3C2',
            '#F5A623',
            '#9013FE'
          ],
        }]
      },
      options: {
        plugins: {
          title: {
            display: true,
            text: 'Distribution of Patient Groups',
            font: {
              size: 14
            },
            padding: {
              top: 40,
              bottom: 30
            }
          },
          legend: {
            position: 'top'
          }
        }
      }
    })
  })
  </script>
