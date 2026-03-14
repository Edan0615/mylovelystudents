<template>
</template>

<script setup>
//composition api
//option api
import {computed,onMounted} from 'vue';
let apple = computed(() => {
  return 'apple'
})
onMounted(() => {
  console.log('Myinfo is mounted successfully...')
})
</script>