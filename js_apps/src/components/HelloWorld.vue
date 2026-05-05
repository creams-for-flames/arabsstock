<template>
  <div class="hello">
    <img alt="Vue logo" src="./../assets/logo.png">
    <h1>{{ msg }}</h1>
  </div>
</template>

<script>
export default {
  name: 'HelloWorld',
  props: {
    msg: String
  }
}
</script>
