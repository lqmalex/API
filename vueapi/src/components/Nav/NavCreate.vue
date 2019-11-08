<template>
  <div class="Nav">
    <a-form>
      <a-form-item label="名称" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input
          v-model="name"
        />
      </a-form-item>
      <a-form-item label="位置" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-select v-model="positionId" @change="handleProvinceChange">
          <a-select-option v-for="(val,key) in positionList" v-model="key">
            {{val}}
          </a-select-option>
        </a-select>
      </a-form-item>
      <a-form-item v-show="type" label="图片" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-upload
          :customRequest="customRequest"
          listType="picture-card"
          @preview="handlePreview"
          @change="handleChange"
          :remove="(file)=>Remove(file)"
        >
          <div v-if="fileList.length < 1">
            <a-icon type="plus"/>
            <div class="ant-upload-text">Upload</div>
          </div>
        </a-upload>
        <a-modal :visible="previewVisible" :footer="null" @cancel="handleCancel">
          <img alt="example" style="width: 100%" :src="previewImage"/>
        </a-modal>
      </a-form-item>
      <a-form-item label="链接类型" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-select v-model="linkTypeId">
          <a-select-option v-for="(val,key) in linkType" v-model="key">
            {{val}}
          </a-select-option>
        </a-select>
      </a-form-item>
      <a-form-item label="链接目标" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-Select v-model="linkTarget">
          <a-select-option v-for="(val,key) in List" v-model="val.id" onSelect="LinkChange">
            {{val.name}}
          </a-select-option>
        </a-Select>
      </a-form-item>
      <a-form-item :wrapper-col="{ span: 12, offset: 5 }">
        <a-button type="primary" html-type="submit" @click="toAdd">
          Submit
        </a-button>
      </a-form-item>
    </a-form>
  </div>
</template>

<script>
    import qs from 'qs';
    import Api from "../../Api";

    export default {
        data() {
            return {
                fileKey: null,
                type: false,
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                previewVisible: false,
                previewImage: '',
                fileList: [],
                positionId: '1',
                linkTypeId: '1',
                linkTarget: '',
                positionList: {
                    "1": "顶部导航",
                    "2": "banner图",
                    "3": "icon",
                    "4": "4张大图",
                },
                //链接类型
                linkType: {
                    "1": "商品分类页面",
                    "2": '商品购买页面',
                    "3": '商品活动页面',
                    "4": '店铺',
                },
                id: '',
                data: {},
                List: [],
                name: '',
                positionAndLinks: [],
                fileValue: null,
            }
        },
        methods: {
            handleCancel() {
                this.previewVisible = false;
            },
            Remove() {
                this.fileList = [];
                this.fileKey = null;
            },
            customRequest(file) {
                const formData = new FormData();
                formData.append('image', file.file);
                this.toUpload(formData);
            },
            handlePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            handleChange({fileList}) {
                this.fileList = fileList;
            },
            handleProvinceChange(value) {
                if (value != 1) {
                    this.type = true;
                } else {
                    this.type = false;
                }
            },
            toUpload(file) {
                this.axios.post(Api.NavUpload,
                    file,
                    {headers: {'Content-Type': 'multipart/form-data'}}
                ).then((data) => {
                    if (data.data.status) {
                        this.fileList[0].status = 'done';
                        this.fileList[0].url = Api.domain + data.data.fileName;
                        this.previewImage = Api.domain + data.data.fileName;
                        this.fileKey = data.data.key;
                    } else {
                        this.$message.error('上传失败');
                    }
                });
            },
            /**
             * 获取数据
             */
            getData() {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getProduct).then((data) => {
                        resolve(this.positionAndLinks[2] = data.data.data.data,
                            this.positionAndLinks[3] = [],
                            this.positionAndLinks[4] = [],);
                    });
                });
            },
            getData2() {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getCate + '?total=0').then((data) => {
                        resolve(this.positionAndLinks[1] = data.data.data.data);
                    });
                });
            },
            /**
             * 更新 链接目标
             */
            setLink(val) {
                if (this.positionAndLinks[val] !== undefined && this.positionAndLinks[val] !== null && this.positionAndLinks[val].length != 0) {
                    this.List = this.positionAndLinks[val];
                    this.linkTarget = this.positionAndLinks[val][0].id;
                } else {
                    this.List = [];
                    if (this.positionAndLinks[val] != undefined) {
                        this.linkTarget = '';
                    }
                }
            },
            getFile() {
                if (this.fileKey != null) {
                    return new Promise((resolve, reject) => {
                        this.axios.post(Api.move, qs.stringify({
                            key: this.fileKey,
                        })).then((res) => {
                            resolve(this.fileValue = res.data);
                        });
                    });
                } else {
                    return new Promise((resolve, reject) => {
                        resolve();
                    });
                }
            },
            toAdd() {
                this.getFile().then(() => {
                    let data = qs.stringify({
                        'title': this.name,
                        'position_id': this.positionId,
                        'picture': this.fileValue === null ? null : Api.domain + this.fileValue,
                        'link_type': this.linkTypeId,
                        'link_target': this.linkTarget,
                    });

                    this.axios.post(Api.NavCreate, data).then((data) => {
                        if (data.data.status) {
                            this.$router.push({path: '/'});
                        } else {
                            this.$message.error(data.data.msg);
                        }
                    });
                })
            }
        },
        /**
         * 监听select
         */
        watch: {
            linkTypeId(val, oldval) {
                this.setLink(val);
            }
        },
        created() {
            this.getData().then(() => {
                this.getData2().then(() => {
                    this.linkTarget = this.positionAndLinks[this.linkTypeId][0].id;
                    this.List = this.positionAndLinks[this.linkTypeId];
                })
            });
        }
    }
</script>

<style lang="scss">
  .ant-upload-select-picture-card {
    i {
      font-size: 32px;
      color: #999;
    }

    .ant-upload-text {
      margin-top: 8px;
      color: #666;
    }
  }
</style>
