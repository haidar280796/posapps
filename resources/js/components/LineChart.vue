<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

interface Props {
  chartData: Object | any,
  chartLabel: string
}

const props = defineProps<Props>();

const chartCanvas = ref<any>(null);

onMounted(() => {
  new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels: props.chartData.labels,
      datasets: [{
        label: props.chartLabel,
        data: props.chartData.values,
        borderColor: '#4CAF50',
        backgroundColor: 'rgba(76, 175, 80, 0.2)',
        borderWidth: 2,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
});
</script>

<template>
  <div class="w-full h-64">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
