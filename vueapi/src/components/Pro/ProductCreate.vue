<template>
  <div class="Product-Create">
    <a-form>
      <a-form-item label="名称" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
        <a-input
          v-model="name"
        />
      </a-form-item>
      <a-form-item label="分类" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
        <a-select v-model="CateId" @change="changeCate(CateId)">
          <a-select-option v-for="(val,key) in cateData" v-model="val.id">
            {{val.name}}
          </a-select-option>
        </a-select>
      </a-form-item>
      <a-form-item label="排序" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
        <a-input-number v-model="sort" :style="{'width':'100%'}"/>
      </a-form-item>
      <a-form-item label="描述" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
        <quill-editor
          v-model="content"
          ref="myQuillEditor"
          :options="editorOption"
          @blur="onEditorBlur($event)" @focus="onEditorFocus($event)"
          @change="onEditorChange($event)">
        </quill-editor>
      </a-form-item>

      <div class="div_box" v-for="(k, index) in form.getFieldValue('keys')" :key="k + 5">
        <a-form-item label="标签类型" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-select v-model="TagId[k].id">
            <a-select-option v-for="(val,key) in tagType" v-model="key">
              {{val}}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="标签值" v-if="TagId[k].id == 1" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-date-picker @change="(date,dateString)=>onChangeDate(date,dateString,k)"/>
        </a-form-item>
        <a-form-item label="标签值" v-else-if="TagId[k].id == 2" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-input v-model="TagValue[k].value"></a-input>
        </a-form-item>
        <a-form-item label="标签值" v-else-if="TagId[k].id == 3" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-upload
            :customRequest="(file)=>customRequest(file,k)"
            listType="picture-card"
            @change="({fileList}) => handleChangeFile({fileList},k)"
            :remove="(file)=>Remove(file,k)"
          >
            <div v-if="TagValue[k].value == ''">
              <a-icon type="plus"/>
              <div class="ant-upload-text">Upload</div>
            </div>
          </a-upload>
          <a-modal :visible="previewVisible" :footer="null" @cancel="handleCancel">
            <img alt="example" style="width: 100%" :src="previewImage"/>
          </a-modal>
        </a-form-item>
        <a-icon
          v-if="form.getFieldValue('keys').length > 1"
          class="dynamic-delete-button"
          type="minus-circle-o"
          :disabled="form.getFieldValue('keys').length === 1"
          @click="() => remove(k)"
        />
      </div>

      <div class="tagAdd">
        <a-form-item :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
          <a-button type="dashed" style="width: 60%" @click="add">
            <a-icon type="plus"/>
            继续添加标签
          </a-button>
        </a-form-item>
      </div>

      <div class="div_box" v-for="(k, index) in form.getFieldValue('keys2')" :key="k + 10">
        <a-form-item label="原价" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-input-number v-model="skuValues[k].original_price" :style="{'width':'100%'}"/>
        </a-form-item>
        <a-form-item label="售价" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-input-number v-model="skuValues[k].price" :style="{'width':'100%'}"/>
        </a-form-item>
        <a-form-item v-for="(val,key) in attrs" :key="key" :label="val" :label-col="{ span: 2 }"
                     :wrapper-col="{ span: 20 }">
          <a-input v-model="skuValues[k].attrs[key]"></a-input>
        </a-form-item>
        <a-form-item label="库存" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-input-number v-model="skuValues[k].quantity" :style="{'width':'100%'}"/>
        </a-form-item>
        <a-form-item label="排序" :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-input-number v-model="skuValues[k].sort" :style="{'width':'100%'}"/>
        </a-form-item>
        <a-icon
          v-if="form.getFieldValue('keys2').length > 1"
          class="dynamic-delete-button"
          type="minus-circle-o"
          :disabled="form.getFieldValue('keys2').length === 1"
          @click="() => remove2(k)"
        />
      </div>

      <div class="skuAdd">
        <a-form-item :label-col="{ span: 2 }" :wrapper-col="{ span: 20 }">
          <a-button type="dashed" style="width: 60%" @click="add2">
            <a-icon type="plus"/>
            继续添加库存
          </a-button>
        </a-form-item>
      </div>

      <a-button type="primary" html-type="submit" @click="toCreate">
        Submit
      </a-button>
    </a-form>
  </div>
</template>

<script>
    import Api from "../../Api";
    import qs from "qs";
    import {quillEditor} from 'vue-quill-editor'

    export default {
        data() {
            return {
                types: [],
                id: 0,
                id2: 0,
                skuValues: [],
                original_price: 0,
                price: 0,
                quantity: 0,
                sort2: 10,
                attrs: [],
                attrValues: [],
                previewVisible: false,
                previewImage: [],
                fileList: [],
                TagId: [],
                TagValue: [],
                tagType: {
                    1: "保质期",
                    2: "促销宣传语",
                    3: "图片",
                },
                name: '',
                content: null,
                editorOption: {},
                cateData: [],
                CateId: '',
                sort: 10,
                tagObj: [],
                paths: [],
            }
        },
        beforeCreate() {
            this.form = this.$form.createForm(this, {name: 'dynamic_form_item'});
            this.form.getFieldDecorator('keys', {initialValue: [], preserve: true});
            this.form.getFieldDecorator('keys2', {initialValue: [], preserve: true});
        },
        methods: {
            changeCate(id) {
                this.cateData.forEach((val) => {
                    if (val.id == id) {
                        let json = JSON.parse(val.property);
                        this.attrs = [];
                        for (var key in json) {
                            if (json[key] != '') {
                                this.attrs.push(json[key]);
                            }
                        }
                    }
                });
            },
            onChangeDate(date, dateString, k) {
                this.TagValue[k].value = dateString;
            },
            handleCancel() {
                this.previewVisible = false;
            },
            Remove(file, k) {
                this.paths[k] = "";
                this.TagValue[k].value = '';
                this.fileList = null;
                ///////////////////////////////////
                // this.TagValue[k].value = null;// 坑货
                ///////////////////////////////////
            },
            customRequest(file, k) {
                const formData = new FormData();
                formData.append('image', file.file);
                this.toUpload(formData, k).then(() => {
                    this.types[k] = false;
                    this.getFile(k, this.TagValue[k].value);
                });
            },
            handlePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            handleChangeFile({fileList}, k) {
                this.fileList = fileList;
            },
            /**
             * 上传文件
             */
            toUpload(file, k) {
                return new Promise((resolve, reject) => {
                    this.axios.post(Api.NavUpload,
                        file,
                        {headers: {'Content-Type': 'multipart/form-data'}}
                    ).then((data) => {
                        if (data.data.status) {
                            resolve(this.fileList[0].status = 'done',
                                this.fileList[0].url = Api.domain + data.data.fileName,
                                this.previewImage[0] = Api.domain + data.data.fileName,
                                this.TagValue[k].value = data.data.key);
                        } else {
                            this.$message.error('上传失败');
                        }
                    });
                });
            },
            onEditorBlur() {//失去焦点事件
            },
            onEditorFocus() {//获得焦点事件
            },
            onEditorChange() {//内容改变事件
                // console.log(this.content);
            },
            getCate() {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getCate + '?total=0').then((res) => {
                        if (res.data.status) {
                            resolve(this.cateData = res.data.data.data, this.CateId = res.data.data.data[0].id);
                        }
                    });
                });
            },
            getFile(k, key) {
                return new Promise((resolve, reject) => {
                    this.axios.post(Api.move, qs.stringify({
                        key: key,
                    })).then((res) => {
                        resolve(this.paths[k] = res.data);
                    });
                });
            },
            toCreate() {
                let tagObj = [];
                this.TagId.forEach((val, key) => {
                    var len = Object.keys(val);
                    if (len.length != 0) {
                        if (val.id == 3) {
                            tagObj.push({
                                tag_id: val.id,
                                value: this.paths[key] != '' ? Api.domain + this.paths[key] : null,
                            });
                        } else {
                            tagObj.push({
                                tag_id: val.id,
                                value: this.TagValue[key].value,
                            });
                        }
                    }
                });

                let skuObj = [];
                this.skuValues.forEach((val, key) => {
                    var len = Object.keys(val);

                    if (len.length != 0) {
                        val.attr1 = val.attrs[0] === undefined ? "" : val.attrs[0];
                        val.attr2 = val.attrs[1] === undefined ? "" : val.attrs[1];
                        val.attr3 = val.attrs[2] === undefined ? "" : val.attrs[2];
                        skuObj.push({
                            original_price: val.original_price,
                            price: val.price,
                            quantity: val.quantity,
                            sort: val.sort,
                            attr1: val.attr1,
                            attr2: val.attr2,
                            attr3: val.attr3,
                        })
                    }
                });

                let data = JSON.stringify({
                    category_id: this.CateId,
                    name: this.name,
                    content: this.content,
                    sort: this.sort,
                    tag: tagObj,
                    sku: skuObj
                });

                this.axios.post(Api.ProductCreate, data, {headers: {'Content-Type': 'application/json'}}).then((res) => {
                    if (res.data.status) {
                        this.$router.push({path: '/Product'});
                    } else {
                        this.$message.error(res.data.msg);
                    }
                });
            },
            remove(k) {
                const {form} = this;
                // can use data-binding to get
                const keys = form.getFieldValue('keys');
                // We need at least one passenger
                if (keys.length === 1) {
                    return;
                }
                this.TagId[k] = {};
                this.TagValue[k] = {};
                // can use data-binding to set
                form.setFieldsValue({
                    keys: keys.filter(key => key !== k),
                });
            },
            remove2(k) {
                const {form} = this;
                // can use data-binding to get
                const keys = form.getFieldValue('keys2');
                // We need at least one passenger
                if (keys.length === 1) {
                    return;
                }

                this.skuValues[k] = {};
                // can use data-binding to set
                form.setFieldsValue({
                    keys2: keys.filter(key => key !== k),
                });
            },
            add() {
                const {form} = this;

                const keys = form.getFieldValue('keys');
                const nextKeys = keys.concat(this.id++);

                this.types.push(true);
                this.TagId.push({
                    'id': '1',
                });
                this.TagValue.push({
                    'value': '',
                });

                form.setFieldsValue({
                    keys: nextKeys,
                });
            },
            add2() {
                const {form} = this;

                const keys = form.getFieldValue('keys2');
                const nextKeys = keys.concat(this.id2++);
                this.skuValues.push({
                    'original_price': 0,
                    'price': 0,
                    'attrs': [],
                    'quantity': 0,
                    'sort': 10,
                });

                form.setFieldsValue({
                    keys2: nextKeys,
                });
            },
        },
        created() {
            this.getCate().then(() => {
                let json = JSON.parse(this.cateData[0].property);
                for (var key in json) {
                    if (json[key] != '') {
                        this.attrs.push(json[key]);
                    }
                }
            });
            this.add();
            this.add2();
        }
    }
</script>

<style lang="scss">
  .tagAdd {
    .ant-form-item-control-wrapper {
      width: 100% !important;
    }
  }

  .skuAdd {
    .ant-form-item-control-wrapper {
      width: 100% !important;
    }
  }

  .div_box {
    border-bottom: 1px solid #ccc;
    position: relative;
  }

  .dynamic-delete-button {
    cursor: pointer;
    position: relative;
    top: 4px;
    font-size: 24px;
    color: #999;
    transition: all 0.3s;
    position: absolute;
    top: 5px;
    right: 5%;
  }
</style>
