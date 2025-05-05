<template>
    <canvas id="pieChart"></canvas>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import Chart from 'chart.js/auto'
  import axios from 'axios'

  onMounted(async () => {
    const res = await axios.get('/api/chart/pie', {
      withCredentials: true, // Important for sending credentials with requests
    });
    const labels = res.data.map(item => item.group)
    const data = res.data.map(item => item.total)

    new Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          label: 'Patient Groups',
          data,
          backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#66BB6A'],
        }]
      }
    })
  })
  </script>
