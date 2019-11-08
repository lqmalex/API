<template>
  <div class="CateCreate">
    <a-form>
      <a-form-item label="名称" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="name"/>
      </a-form-item>
      <a-form-item label="属性1" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="property1"/>
      </a-form-item>
      <a-form-item label="属性2" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="property2"/>
      </a-form-item>
      <a-form-item label="属性3" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="property3"/>
      </a-form-item>
      <a-form-item label="排序" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="sort"/>
      </a-form-item>
      <a-form-item :wrapper-col="{ span: 12, offset: 5 }">
        <a-button type="primary" html-type="submit" @click="toCreate">Submit</a-button>
      </a-form-item>
    </a-form>
  </div>
</template>

<script>
    import qs from "qs";
    import Api from "../../Api";

    export default {
        data() {
            return {
                name: '',
                property1: '',
                property2: '',
                property3: '',
                sort: 10
            }
        },
        methods: {
            toCreate() {
                let property = JSON.stringify({
                    "attr1": this.property1 === null ? '' : this.property1,
                    "attr2": this.property2 === null ? '' : this.property2,
                    "attr3": this.property3 === null ? '' : this.property3,
                });

                let data = qs.stringify({
                    name: this.name,
                    property: property,
                    sort: this.sort,
                });

                this.axios.post(Api.CateCreate, data).then((data) => {
                    if (data.data.status) {
                        this.$router.push('/Cate');
                    } else {
                        this.$message.error(data.data.msg);
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
</style>
