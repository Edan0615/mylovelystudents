<template>
  <button :class="['btn', is_happy ? 'btn-success' : 'btn-danger']">
    {{ is_happy ? '我很快樂' : '我不開心' }}
  </button>
  
</template>

<script setup>
import { onMounted } from 'vue';

// 定義 Props，建議加上型別限制更專業
const props = defineProps({
  is_happy: {
    type: Boolean,
    default: false
  }
});

onMounted(() => {
  console.log("組件已掛載，目前心情狀態：", props.is_happy);
});
</script>