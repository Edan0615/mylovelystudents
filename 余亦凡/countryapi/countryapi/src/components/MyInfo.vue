<template>
  <button @click="sayHello" class="btn btn-primary">Click me</button>
</template>

<script setup>
    function sayHello() {
        alert("Hello");
    }
</script>
