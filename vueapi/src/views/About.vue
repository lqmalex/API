<template>
  <div class="about">
    <h1>This is an about page</h1>
    <a-button type="primary">Button</a-button>
  </div>
</template>

<script>
import {Button} from 'ant-design-vue';

export default {
    components:{
        [Button.name]:Button,
    },
}
</script>

<style lang="scss">

</style>
