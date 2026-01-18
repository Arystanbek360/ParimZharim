<!-- Loader.vue -->
<template>
    <div class="loader" v-if="show">
      <div class="spinner"></div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      show: {
        type: Boolean,
        default: false,
      },
    },
  };
  </script>
  
  <style scoped>
  .loader {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.7);
    z-index: 10;
  }
  
  .spinner {
    border: 10px solid #f3f3f3;
    border-top: 10px solid #0ea5ecb8;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin-a3277d48 2s linear infinite;
  }
  
  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
  </style>
  