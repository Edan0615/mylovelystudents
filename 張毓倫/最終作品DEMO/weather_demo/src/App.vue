<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <!-- Using the newly created WeatherCard component -->
        <WeatherCard />
      </div>
    </div>
  </div>
</template>

<script setup>
import WeatherCard from './components/WeatherCard.vue'
</script>

<style>
/* Global styles can remain here */
body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
}

#app {
  width: 100%;
}
</style>
