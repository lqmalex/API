<template>
  <div class="Product">
    <a-table :columns="columns" :dataSource="dataSource" bordered :pagination="false">

      <template slot="shelf" slot-scope="text,record,index">
        <a-switch v-if="record.status == 1" checkedChildren="上架" unCheckedChildren="下架" defaultChecked
                  @change="onChangeShelf(record.id,record.status,record)"/>
        <a-switch v-else checkedChildren="上架" unCheckedChildren="下架"
                  @change="onChangeShelf(record.id,record.status,record)"/>
      </template>

      <div
        slot="filterDropdown"
        slot-scope="{ setSelectedKeys, selectedKeys, confirm, clearFilters, column }"
        style="padding: 8px"
      >
        <a-input
          v-ant-ref="c => searchInput = c"
          :placeholder="`Search ${column.dataIndex}`"
          :value="selectedKeys[0]"
          @change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])"
          @pressEnter="() => handleSearch(selectedKeys, confirm)"
          style="width: 188px; margin-bottom: 8px; display: block;"
        />
        <a-button
          type="primary"
          @click="() => handleSearch(selectedKeys, confirm)"
          icon="search"
          size="small"
          style="width: 90px; margin-right: 8px"
        >Search
        </a-button
        >
        <a-button @click="() => handleReset(clearFilters)" size="small" style="width: 90px"
        >Reset
        </a-button
        >
      </div>

      <template
        v-for="col in ['name', 'sort','category_name']"
        :slot="col"
        slot-scope="text, record, index"
      >
        <div>
          <a-input
            v-if="record.editable && col != 'category_name' && col != 'sort'"
            style="margin: -5px 0"
            :value="text"
            @change="e => handleChange(e.target.value, record.key, col,'pro')"
          />
          <a-input-number v-else-if="record.editable && col == 'sort'" v-model="ProSort"
                          @change="handleChange(ProSort,record.key, col,'pro')"/>
          <a-select v-else-if="record.editable && col == 'category_name'" v-model="cateId"
                    @change="onChange(cateId,record,col,cateId)">
            <a-select-option v-for="(val,key) in cateArr" v-model="val.id">
              {{val.name}}
            </a-select-option>
          </a-select>
          <template v-else
          >{{text}}
          </template
          >
        </div>
      </template>

      <template slot="operation" slot-scope="text, record, index">
        <a-popconfirm
          v-if="data.length"
          title="确定删除?"
          @confirm="() => onDelete(record)"
        >
          <a style="color: red">删除</a>
        </a-popconfirm>
        &nbsp;&nbsp;
        <span>
          <a @click="() => getContent(record)">描述</a>
        </span>&nbsp;&nbsp;
        <a type="primary" @click="showDrawer(index,record.id)">标签</a>&nbsp;&nbsp;
        <a type="primary" @click="showDrawer2(record.category_id,index,record.id)">库存</a>&nbsp;&nbsp;
        <span v-if="record.editable">
          <a @click="() => save(record.key,'pro')">执行编辑</a>&nbsp;&nbsp;
          <a @click="() => cancel(record.key,'pro')">取消编辑</a>
        </span>
        <span v-else>
          <a @click="() => edit(record,'pro')">编辑</a>
        </span>
      </template>
    </a-table>
    <a-pagination :pageSize="pageSize" @change="PageChange" :current="page" :defaultCurrent="1" :total="total"/>
    <!--  标签  -->
    <a-drawer
      title="标签"
      placement="bottom"
      height=""
      :closable="true"
      @close="onClose"
      :visible="visible"
    >
      <div class="Table">
        <a-button type="primary" @click="showModal('tag')">添加标签</a-button>
        <a-table :columns="columns2" :dataSource="dataSource2" bordered rowKey="id"
                 :pagination="{pageSize:2,defaultCurrent:1,total:skuOrtagTotal}">
          <template
            v-for="col in ['tag_type','value']"
            :slot="col"
            slot-scope="text, record, index"
          >
            <div>
              <a-input
                v-if="record.editable && col != 'tag_type' && tagId == '2'"
                style="margin: -5px 0"
                :value="text"
                @change="e => handleChange(e.target.value, record.key, col,'tag')"
              />
              <template v-else-if="record.editable && col != 'tag_type' && tagId == '1'">
                <a-date-picker @change="(date,dateString)=>onChangeDate(dateString,record,col,'tag')"/>
              </template>
              <template v-else-if="record.editable && col != 'tag_type' && tagId == '3'">
                <a-upload
                  :customRequest="(file)=>customRequest(file,record,col,'tag')"
                  listType="picture-card"
                  @preview="handlePreview"
                  @change="handleChangeFile"
                  :remove="Remove"
                >
                  <div v-if="fileList.length < 1">
                    <a-icon type="plus"/>
                    <div class="ant-upload-text">Upload</div>
                  </div>
                </a-upload>
                <a-modal :visible="previewVisible" :footer="null" @cancel="handleCancel">
                  <img alt="example" style="width: 100%" :src="previewImage"/>
                </a-modal>
              </template>
              <a-select v-else-if="record.editable && col == 'tag_type'" v-model="tagId"
                        @change="onChange2(tagId,record,col)">
                <a-select-option v-for="(val,key) in tagType" v-model="key">
                  {{val}}
                </a-select-option>
              </a-select>
              <template v-else-if="col == 'value' && record.tag_id != '3'">{{text}}</template>
              <template v-else-if="col == 'value' && record.tag_id == '3'">
                <img :src="text" width="70px">
              </template>
              <template v-else>{{text}}</template>
            </div>
          </template>

          <template slot="operation" slot-scope="text, record, index">
            <div class="editable-row-operations">
        <span v-if="record.editable">
          <a @click="() => save(record.key,'tag')">执行编辑</a>&nbsp;
          <a @click="() => cancel(record.key,'tag')">取消编辑</a>
        </span>
              <span v-else>
          <a @click="() => edit(record,'tag')">编辑</a>
        </span>
            </div>
          </template>
        </a-table>
      </div>

    </a-drawer>
    <!--标签end-->

    <!--   库存   -->
    <a-drawer
      title="库存"
      placement="bottom"
      height=""
      :closable="true"
      @close="onClose2"
      :visible="visible2"
    >

      <a-button type="primary" @click="showModal('sku')">添加库存</a-button>
      <a-table :columns="columns3" :dataSource="dataSource3" bordered rowKey="id"
               :pagination="{pageSize:2,defaultCurrent:1,total:skuOrtagTotal}">
        <template
          v-for="col in ['original_price', 'price', 'attr1','attr2','attr3','quantity','sort']"
          :slot="col"
          slot-scope="text, record, index"
        >
          <div :key="col">
            <a-input
              v-if="record.editable"
              style="margin: -5px 0"
              :value="text"
              @change="e => handleChange(e.target.value, record.key, col,'sku')"
            />
            <template v-else
            >{{text}}
            </template
            >
          </div>
        </template>


        <template slot="operation" slot-scope="text, record, index">
          <div class="editable-row-operations">
        <span v-if="record.editable">
          <a @click="() => save(record.key,'sku')">执行编辑</a>
          <a-popconfirm title="确定取消?" @confirm="() => cancel(record.key,'sku')">
            <a>取消编辑</a>
          </a-popconfirm>
        </span>
            <span v-else>
          <a @click="() => edit(record,'sku')">编辑</a>
        </span>
          </div>
        </template>
      </a-table>
    </a-drawer>

    <!-- 添加标签-->
    <a-modal
      title="添加标签"
      :visible="tagVisibleAdd"
      @ok="handleOk('tag')"
      :confirmLoading="false"
      @cancel="handleCancelAdd('tag')"
    >
      <a-form-item label="标签类型" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-select v-model="TagAddId" @change="()=>{TagAddValue=''}">
          <a-select-option v-for="(val,key) in tagType" v-model="key">
            {{val}}
          </a-select-option>
        </a-select>
      </a-form-item>
      <a-form-item label="标签值" v-if="TagAddId == 1" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-date-picker @change="(date,dateString)=>{TagAddValue = dateString}"/>
      </a-form-item>
      <a-form-item label="标签值" v-else-if="TagAddId == 2" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input v-model="TagAddValue"></a-input>
      </a-form-item>
      <a-form-item label="标签值" v-else-if="TagAddId == 3" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-upload
          :customRequest="(file)=>customRequest(file,'','','')"
          listType="picture-card"
          @change="({fileList}) => handleChangeFile({fileList})"
          :remove="Remove"
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
    </a-modal>

    <!-- 添加库存-->
    <a-modal
      title="添加库存"
      :visible="skuVisibleAdd"
      @ok="handleOk('sku')"
      :confirmLoading="false"
      @cancel="handleCancelAdd('sku')"
    >
      <a-form-item label="原价" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input-number v-model="original_price" :style="{'width':'100%'}"/>
      </a-form-item>
      <a-form-item label="售价" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input-number v-model="price" :style="{'width':'100%'}"/>
      </a-form-item>
      <a-form-item v-for="(val,key) in attrs" :key="key" :label="val" :label-col="{ span: 5 }"
                   :wrapper-col="{ span: 12 }">
        <a-input v-model="attrValues[key]"></a-input>
      </a-form-item>
      <a-form-item label="库存" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input-number v-model="quantity" :style="{'width':'100%'}"/>
      </a-form-item>
      <a-form-item label="排序" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
        <a-input-number v-model="sort2" :style="{'width':'100%'}"/>
      </a-form-item>
    </a-modal>

    <!-- 商品描述   -->
    <a-modal
      title="添加标签"
      :visible="ContentVisible"
      @ok="contentOK"
      :confirmLoading="false"
      @cancel="handleCancelContent"
    >
      <quill-editor
        v-model="content"
        ref="myQuillEditor"
        :options="editorOption"
        @blur="onEditorBlur($event)" @focus="onEditorFocus($event)"
        @change="onEditorChange($event)">
      </quill-editor>
    </a-modal>

  </div>
</template>

<script>
    import Api from "../../Api";
    import qs from "qs";
    import {quillEditor} from 'vue-quill-editor'

    export default {
        data() {
            return {
                ContentVisible: false,
                content: null,
                editorOption: {},
                skuOrtagTotal: 0,
                attrs: [],
                attrValues: [],
                original_price: 0,
                price: 0,
                quantity: 0,
                sort2: 10,
                TagAddId: '1',
                TagAddValue: '',
                pid: '',
                tagVisibleAdd: false,
                skuVisibleAdd: false,
                pageSize: 0,
                total: 0,
                page: 1,
                previewVisible: false,
                previewImage: '',
                fileList: [],
                tagType: {
                    1: "保质期",
                    2: "促销宣传语",
                    3: "图片",
                },
                tagType2: {
                    1: "保质期",
                    2: "促销宣传语",
                    3: "图片",
                },
                tagId: '',
                visible: false,
                visible2: false,
                cateArr: [],
                data: [],
                dataSource2: [],
                dataSource3: [],
                dataSource: [],
                columns3: [
                    {
                        title: 'id',
                        key: 'id',
                        dataIndex: 'id',
                        scopedSlots: {customRender: 'id'},
                    },
                    {
                        title: '原价',
                        key: 'original_price',
                        dataIndex: 'original_price',
                        scopedSlots: {customRender: 'original_price'},
                    },
                    {
                        title: '售价',
                        key: 'price',
                        dataIndex: 'price',
                        scopedSlots: {customRender: 'price'},
                    },
                    {
                        title: '库存',
                        key: 'quantity',
                        dataIndex: 'quantity',
                        scopedSlots: {customRender: 'quantity'},
                    },
                    {
                        title: '排序',
                        key: 'sort',
                        dataIndex: 'sort',
                        scopedSlots: {customRender: 'sort'},
                    }
                ],
                columns2: [
                    {
                        title: 'id',
                        key: 'id',
                        dataIndex: 'id',
                        scopedSlots: {customRender: 'id'},
                    },
                    {
                        title: '标签类型',
                        key: 'tag_type',
                        dataIndex: 'tag_type',
                        scopedSlots: {customRender: 'tag_type'},
                    },
                    {
                        title: '标签值',
                        key: 'value',
                        dataIndex: 'value',
                        scopedSlots: {customRender: 'value'},
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        dataIndex: 'created_at',
                        scopedSlots: {customRender: 'created_at'},
                    },
                    {
                        title: '更新时间',
                        key: 'updated_at',
                        dataIndex: 'updated_at',
                        scopedSlots: {customRender: 'updated_at'},
                    },
                    {
                        title: '操作',
                        key: 'operation',
                        dataIndex: 'operation',
                        scopedSlots: {customRender: 'operation'},
                    }
                ],
                columns: [
                    {
                        title: 'id',
                        key: 'id',
                        dataIndex: 'id',
                        scopedSlots: {customRender: 'id'},
                    },
                    {
                        title: '名称',
                        key: 'name',
                        dataIndex: 'name',
                        scopedSlots: {filterDropdown: 'filterDropdown', filterIcon: 'filterIcon', customRender: 'name'},
                    },
                    {
                        title: '分类',
                        key: 'category_name',
                        dataIndex: 'category_name',
                        scopedSlots: {customRender: 'category_name'},
                    },
                    {
                        title: '销量',
                        key: 'sale_num',
                        dataIndex: 'sale_num',
                        scopedSlots: {customRender: 'sale_num'},
                    },
                    {
                        title: '排序',
                        key: 'sort',
                        dataIndex: 'sort',
                        scopedSlots: {customRender: 'sort'},
                    },
                    {
                        title: '上/下架',
                        key: 'shelf',
                        dataIndex: 'shelf',
                        scopedSlots: {customRender: 'shelf'},
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        dataIndex: 'created_at',
                        scopedSlots: {customRender: 'created_at'},
                    },
                    {
                        title: '更新时间',
                        key: 'updated_at',
                        dataIndex: 'updated_at',
                        scopedSlots: {customRender: 'updated_at'},
                    },
                    {
                        title: '操作',
                        key: 'operation',
                        dataIndex: 'operation',
                        scopedSlots: {customRender: 'operation'},
                    }
                ],
                cateId: '',
                ProSort: '',
                defaulValue: '',
                searchName: '',
            }
        },
        methods: {
            getContent(record) {
                this.content = record.content;
                this.ContentVisible = true;
                this.pid = record.id;
            },
            onEditorBlur() {//失去焦点事件
            },
            onEditorFocus() {//获得焦点事件
            },
            onEditorChange() {//内容改变事件
                // console.log(this.content);
            },
            contentOK() {
                let data = JSON.stringify({
                    'pro': {
                        id: this.pid,
                        content: this.content,
                    }
                });

                this.axios.post(Api.ProductEdit, data).then((res) => {
                    if (res.data.status) {
                        this.$message.info('修改成功');
                    } else {
                        this.$message.error('修改失败');
                    }
                }).catch((err) => {
                    this.$message.error('修改失败');
                });
            },
            handleCancelContent() {
                this.ContentVisible = false;
            },
            handleCancelAdd(type) {
                if (type == 'tag') {
                    this.tagVisibleAdd = false;
                } else {
                    this.skuVisibleAdd = false;
                }
                this.tagType = {
                    1: "保质期",
                    2: "促销宣传语",
                    3: "图片",
                };
            },
            handleOk(type) {
                let pid;
                let data;
                if (type == 'tag') {
                    data = {
                        pid: this.pid,
                        tag: {
                            tag_id: this.TagAddId,
                            value: this.TagAddValue,
                        }
                    };
                } else {
                    let obj = {
                        attr1: this.attrValues[0] === undefined ? null : this.attrValues[0],
                        attr2: this.attrValues[1] === undefined ? null : this.attrValues[1],
                        attr3: this.attrValues[2] === undefined ? null : this.attrValues[2]
                    };

                    data = {
                        pid: this.pid,
                        sku: {
                            original_price: this.original_price,
                            price: this.price,
                            attr1: obj.attr1,
                            attr2: obj.attr2,
                            attr3: obj.attr3,
                            quantity: this.quantity,
                            sort: this.sort2,
                        }
                    }
                }

                this.axios.post(Api.ProductCreate, data, {headers: {'Content-Type': 'application/json'}}).then((res) => {
                    if (res.data.status) {
                        this.setData('',this.page);
                        this.$message.info('添加成功');
                        this.tagVisibleAdd = false;
                        this.skuVisibleAdd = false;
                        this.visible = false;
                        this.visible2 = false;
                    } else {
                        this.$message.error('添加失败');
                        this.tagVisibleAdd = false;
                        this.skuVisibleAdd = false;
                    }
                });
            },
            showModal(type) {
                if (type == 'tag') {
                    this.tagVisibleAdd = true;
                } else {
                    this.skuVisibleAdd = true;
                }
            },
            /**
             * 上 / 下架
             */
            onChangeShelf(id, type, record) {
                type = Number(type);
                let num = type == 1 ? 2 : 1;

                this.axios.post(Api.ChangeShelf, qs.stringify({id: id, type: num})).then((res) => {
                    if (res.data.status) {
                        record.status = num;
                        this.$message.info('修改成功');
                    } else {
                        this.$message.error('修改失败');
                    }
                });
            },
            PageChange(page, pageSize) {
                this.page = page;
                this.setData(this.searchName, page);
            },
            /**
             *时间 更改
             */
            onChangeDate(dateString, record, column, type) {
                record.value = dateString;
                record.tag_id = 1;
                record.tag_type = this.tagType[1];
            },
            handleCancel() {
                this.previewVisible = false;
            },
            Remove() {
                this.fileList = [];
            },
            customRequest(file, record, column, type) {
                const formData = new FormData();
                formData.append('image', file.file);
                this.toUpload(formData, record, column, type);
            },
            handlePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            handleChangeFile({fileList}) {
                this.fileList = fileList;
            },
            /**
             * 上传文件
             */
            toUpload(file, record, column, type) {
                this.axios.post(Api.NavUpload,
                    file,
                    {headers: {'Content-Type': 'multipart/form-data'}}
                ).then((data) => {
                    if (data.data.status) {
                        this.fileList[0].status = 'done';
                        this.fileList[0].url = Api.domain + data.data.fileName;
                        this.previewImage = Api.domain + data.data.fileName;
                        if (record != '') {
                            record.tag_id = 3;
                            record.tag_type = this.tagType[3];
                            record.value = Api.domain + data.data.fileName;
                        } else {
                            this.TagAddValue = Api.domain + data.data.fileName;
                        }
                    } else {
                        this.$message.error('上传失败');
                    }
                });
            },
            /**
             * 搜索
             */
            handleSearch(selectedKeys, confirm) {
                confirm();
                this.dataSource = [];
                this.page = 1;
                this.searchName = selectedKeys[0];
                this.setData(selectedKeys[0]);
            },
            /**
             * 取消搜索
             */
            handleReset(clearFilters) {
                clearFilters();
                this.dataSource = [];
                this.searchName = '',
                    this.setData('', this.page);
            },
            onChange2(value, record, column) {
                record.tag_id = value;
                record.value = '';
            },
            /**
             * 更改 分类
             */
            onChange(value, record, column) {
                record.category_id = value;
                let name;
                this.cateArr.forEach((val) => {
                    if (val.id == value) {
                        name = val.name;
                    }
                });

                this.handleChange(name, record.key, column, 'pro');
            },
            handleChange(value, key, column, type) {
                var newData;
                var target;
                switch (type) {
                    case 'pro':
                        newData = [...this.dataSource];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            target[column] = value;
                            this.dataSource = newData;
                        }
                        break;
                    case 'tag':
                        newData = [...this.dataSource2];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            target[column] = value;
                            this.dataSource2 = newData;
                        }
                        break;
                    case 'sku':
                        newData = [...this.dataSource3];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            target[column] = value;
                            this.dataSource3 = newData;
                        }
                        break;
                    default:
                        break;
                }
            },
            edit(record, type) {
                var newData;
                var target;
                switch (type) {
                    case 'pro':
                        let key = record.key;
                        this.cateId = record.category_id;
                        this.ProSort = record.sort;
                        newData = [...this.dataSource];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            target.editable = true;
                            this.dataSource = newData;
                        }
                        break;
                    case 'tag':
                        newData = [...this.dataSource2];
                        this.tagId = record.tag_id.toString();
                        this.defaulValue = record.value;
                        target = newData.filter(item => record.key === item.key)[0];
                        if (target) {
                            target.editable = true;
                            this.dataSource2 = newData;
                        }
                        break;
                    case 'sku':
                        newData = [...this.dataSource3];
                        target = newData.filter(item => record.key === item.key)[0];
                        if (target) {
                            target.editable = true;
                            this.dataSource3 = newData;
                        }
                        break;
                    default:
                        break;
                }
            },
            save(key, type) {
                var newData;
                var target;
                switch (type) {
                    case 'pro':
                        newData = [...this.dataSource];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            this.dataSource = newData;
                            this.toEdit(type, target);
                        }
                        break;
                    case 'tag':
                        newData = [...this.dataSource2];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            this.dataSource2 = newData;
                            this.toEdit(type, target);
                        }
                        break;
                    case 'sku':
                        newData = [...this.dataSource3];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            this.dataSource3 = newData;
                            this.toEdit(type, target);
                        }
                        break;
                    default:
                        break;
                }
            },
            cancel(key, type) {
                var newData;
                var target;
                switch (type) {
                    case 'pro':
                        newData = [...this.dataSource];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            this.dataSource = newData;
                        }
                        break;
                    case 'tag':
                        newData = [...this.dataSource2];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            target.value = this.defaulValue;
                            this.dataSource2 = newData;
                        }
                        break;
                    case 'sku':
                        newData = [...this.dataSource3];
                        target = newData.filter(item => key === item.key)[0];
                        if (target) {
                            delete target.editable;
                            this.dataSource3 = newData;
                        }
                        break;
                    default:
                        break;
                }
            },
            /**
             * 商品数据
             * @returns {Promise<unknown>}
             */
            getData(name = '', page = '') {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getProduct + `?name=${name}&page=${page}`).then((res) => {
                        if (res.data.status) {
                            resolve(this.data = res.data.data.data, this.total = res.data.data.total, this.pageSize = res.data.data.per_page);
                        }
                    }).catch((err)=>{
                        if(err.response.status == 429) {
                            this.$message.error('您操作太快,请稍后重试');
                        };
                    });
                });
            },
            /**
             * 软删除
             * @param record
             */
            onDelete(record) {
                let data = qs.stringify({
                    id: record.id,
                })
                this.axios.post(Api.DelProduct, data).then((res) => {
                    if (res.data.status) {
                        const dataSource = [...this.dataSource];
                        this.dataSource = dataSource.filter(item => item.key !== record.key);
                        this.$message.info('删除成功');
                    } else {
                        this.$message.error('删除失败');
                    }
                });
            },
            /**
             * 分类
             * @returns {Promise<unknown>}
             */
            getCate() {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getCate + '?total=0').then((data) => {
                        if (data.data.status) {
                            resolve(this.cateArr = data.data.data.data);
                        }
                    });
                });
            },
            /**
             * 执行编辑
             * @param type
             * @param target
             */
            toEdit(type, target) {
                let data;
                switch (type) {
                    case 'pro':
                        data = JSON.stringify({
                            'pro': {
                                id: target.id,
                                category_id: target.category_id,
                                name: target.name,
                                sort: target.sort,
                            }
                        });
                        break;
                    case 'tag':
                        data = JSON.stringify({
                            'tag': {
                                id: target.id,
                                tag_id: target.tag_id,
                                value: target.value,
                            }
                        });
                        break;
                    case 'sku':
                        data = JSON.stringify({
                            'sku': {
                                id: target.id,
                                original_price: target.original_price,
                                price: target.price,
                                quantity: target.quantity,
                                sort: target.sort,
                                attr1: target.attr1,
                                attr2: target.attr2,
                                attr3: target.attr3,
                            }
                        })
                        break;
                    default:
                        return;
                        break;
                }

                this.axios.post(Api.ProductEdit, data, {headers: {'Content-Type': 'application/json'}}).then((res) => {
                    if (res.data.status) {
                        this.$message.info('编辑成功');
                    } else {
                        this.$message.error('编辑失败');
                    }
                }).catch((err) => {
                    this.$message.error('编辑失败');
                });
            },
            /**
             * 获取对应标签数据
             * @param index
             */
            showDrawer(index, id) {
                this.skuOrtagTotal = 0;
                this.pid = id;
                let arr = this.dataSource[index].tag;
                this.dataSource2 = [];
                arr.forEach((val) => {
                    let obj = {
                        key: val.id,
                        id: val.id,
                        tag_id: val.tag_id,
                        tag_type: this.tagType2[val.tag_id],
                        value: val.value,
                        created_at: val.created_at,
                        updated_at: val.updated_at,
                    }

                    this.dataSource2.push(obj);
                });

                this.skuOrtagTotal = this.dataSource2.length;
                this.visible = true;
            },
            /**
             * 获取对象库存数据
             * @param id
             * @param index
             */
            showDrawer2(id, index, pid) {
                this.skuOrtagTotal = 0;
                this.pid = pid;
                let arr = this.dataSource[index].sku;
                this.dataSource3 = [];
                this.columns3 = [
                    {
                        title: 'id',
                        key: 'id',
                        dataIndex: 'id',
                        scopedSlots: {customRender: 'id'},
                    },
                    {
                        title: '原价',
                        key: 'original_price',
                        dataIndex: 'original_price',
                        scopedSlots: {customRender: 'original_price'},
                    },
                    {
                        title: '售价',
                        key: 'price',
                        dataIndex: 'price',
                        scopedSlots: {customRender: 'price'},
                    },
                    {
                        title: '库存',
                        key: 'quantity',
                        dataIndex: 'quantity',
                        scopedSlots: {customRender: 'quantity'},
                    },
                    {
                        title: '排序',
                        key: 'sort',
                        dataIndex: 'sort',
                        scopedSlots: {customRender: 'sort'},
                    }
                ];
                let json;
                this.cateArr.forEach((val, key) => {
                    if (val.id == id) {
                        json = JSON.parse(val.property);
                    }
                });

                this.attrs = [];
                for (let key in json) {
                    if (json[key] !== null && json[key] != '') {
                        this.attrs.push(json[key]);
                        var obj = {
                            title: json[key],
                            key: key,
                            dataIndex: key,
                            scopedSlots: {customRender: key},
                        }

                        this.columns3.push(obj);
                    }
                }

                this.columns3.push(
                    {
                        title: '创建时间',
                        key: 'created_at',
                        dataIndex: 'created_at',
                        scopedSlots: {customRender: 'created_at'},
                    },
                    {
                        title: '更新时间',
                        key: 'updated_at',
                        dataIndex: 'updated_at',
                        scopedSlots: {customRender: 'updated_at'},
                    },
                    {
                        title: '操作',
                        key: 'operation',
                        dataIndex: 'operation',
                        scopedSlots: {customRender: 'operation'},
                    });

                arr.forEach((val) => {
                    let obj = {
                        key: val.id,
                        id: val.id,
                        attr1: val.attr1 === null || val.attr1 == '' ? null : val.attr1,
                        attr2: val.attr2 === null || val.attr2 == '' ? null : val.attr2,
                        attr3: val.attr3 === null || val.attr3 == '' ? null : val.attr3,
                        original_price: val.original_price,
                        price: val.price,
                        quantity: val.quantity,
                        sort: val.sort,
                        created_at: val.created_at,
                        updated_at: val.updated_at,
                    }

                    this.dataSource3.push(obj);
                });

                this.skuOrtagTotal = this.dataSource3.length;
                this.visible2 = true;
            },
            onClose() {
                this.visible = false;
            },
            onClose2() {
                this.visible2 = false;
            },
            setData(name = '', page = '') {
                this.getData(name, page).then(() => {
                    this.getCate().then(() => {
                        this.data.forEach((val, key) => {
                            ;
                            this.cateArr.forEach((v) => {
                                if (v.id == val.category_id) {
                                    this.data[key].key = val.id;
                                    this.data[key].category_name = v.name;
                                }
                            });
                        });

                        this.dataSource = this.data;
                    });
                });
            }
        },
        created() {
            this.setData();
        }
    }
</script>

<style lang="scss">
  .ant-pagination {
    float: right;
    margin-top: 10px !important;
  }

  .Table {
    overflow: hidden;
  }
</style>
