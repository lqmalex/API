<template>
  <div class="Cate">
    <a-table :columns="columns" :dataSource="data" bordered :pagination="false">
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
      <a-icon
        slot="filterIcon"
        slot-scope="filtered"
        type="search"
        :style="{ color: filtered ? '#108ee9' : undefined }"
      />


      <template
        v-for="col in ['name', 'sort', 'property1','property2','property3']"
        :slot="col"
        slot-scope="text, record, index"
      >
        <div :key="col">
          <a-input
            v-if="record.editable"
            style="margin: -5px 0"
            :value="text"
            @change="e => handleChange(e.target.value, record.key, col)"
          />
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
        <span v-if="record.editable">
          <a @click="() => save(record.key)">执行编辑</a>
          <a @click="() => cancel(record.key)">取消编辑</a>
        </span>
          <span v-else>
          <a @click="() => edit(record.key)">编辑</a>
        </span>
      </template>
    </a-table>
    <a-pagination :pageSize="pageSize" @change="PageChange" :current="page" :defaultCurrent="1" :total="total"/>
  </div>
</template>

<script>
    import Api from "../../Api";
    import qs from 'qs';

    export default {
        data() {
            return {
                pageSize: 0,
                total: 0,
                page: 1,
                data: [],
                pagination: {},
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
                        title: '排序',
                        key: 'sort',
                        dataIndex: 'sort',
                        scopedSlots: {customRender: 'sort'},
                    },
                    {
                        title: '属性1',
                        key: 'property1',
                        dataIndex: 'property1',
                        scopedSlots: {customRender: 'property1'}
                    },
                    {
                        title: '属性2',
                        key: 'property2',
                        dataIndex: 'property2',
                        scopedSlots: {customRender: 'property2'}
                    },
                    {
                        title: '属性3',
                        key: 'property3',
                        dataIndex: 'property3',
                        scopedSlots: {customRender: 'property3'}
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        dataIndex: 'created_at',
                        width: '15%',
                        scopedSlots: {customRender: 'created_at'}
                    },
                    {
                        title: '更新时间',
                        key: 'updated_at',
                        dataIndex: 'updated_at',
                        width: '15%',
                        scopedSlots: {customRender: 'updated_at'}
                    },
                    {
                        title: '操作',
                        key: 'operation',
                        dataIndex: 'operation',
                        width: '15%',
                        scopedSlots: {customRender: 'operation'},
                    },
                ],
                searchName: '',
            }
        },
        methods: {
            PageChange(page, pageSize) {
                this.page = page;
                this.setData(this.searchName, page);
            },
            handleSearch(selectedKeys, confirm) {
                confirm();
                this.data = [];
                this.searchName = selectedKeys[0];
                this.page = 1;
                this.setData(selectedKeys[0]);
            },
            handleReset(clearFilters) {
                clearFilters();
                this.data = [];
                this.searchName = '';
                this.setData('', this.page);
            },
            setData(name = '', page = '') {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getCate + `?name=${name}&page=${page}`).then((res) => {
                        if (res.data.status) {
                            this.data = [];
                            res.data.data.data.forEach((val, key) => {
                                var propertys = JSON.parse(val.property);

                                var obj = {
                                    key: val.id,
                                    id: val.id,
                                    name: val.name,
                                    sort: val.sort,
                                    property1: propertys.attr1 === undefined || propertys.attr1 === null ? null : propertys.attr1,
                                    property2: propertys.attr2 === undefined || propertys.attr2 === null ? null : propertys.attr2,
                                    property3: propertys.attr3 === undefined || propertys.attr3 === null ? null : propertys.attr3,
                                    created_at: val.created_at,
                                    updated_at: val.updated_at,
                                }

                                this.data.push(obj);
                            });

                            resolve(this.total = res.data.data.total, this.pageSize = res.data.data.per_page);
                        }
                    });
                })
            },
            onDelete(record) {
                let data = qs.stringify({
                    "id": record.id,
                });
                this.axios.post(Api.DelCate, data).then((data) => {
                    if (data.data.status) {
                        const dataSource = [...this.data];
                        this.data = dataSource.filter(item => item.key !== record.key);
                        this.$message.info('删除成功');
                    } else {
                        this.$message.error('删除失败');
                    }
                });
            },
            handleChange(value, key, column) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target[column] = value;
                    this.data = newData;
                }
            },
            edit(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target.editable = true;
                    this.data = newData;
                }
            },
            save(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    delete target.editable;
                    this.data = newData;
                    this.ToEdit(target);
                }
            },
            cancel(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    delete target.editable;
                    this.data = newData;
                }
            },
            ToEdit(data) {
                let id = data.id;
                let property = JSON.stringify({
                    "attr1": data.property1 === null ? '' : data.property1,
                    "attr2": data.property2 === null ? '' : data.property2,
                    "attr3": data.property3 === null ? '' : data.property3,
                });

                let newData = qs.stringify({
                    name: data.name,
                    sort: data.sort,
                    property: property,
                });

                this.axios.post(Api.CateEdit + id, newData).then((data) => {
                    if (data.data.status) {
                        this.$message.info('编辑成功');
                    } else {
                        this.$message.error('编辑失败');
                    }
                });
            },
        },
        created() {
            this.setData();
        }
    }
</script>

<style>
  .Cate-create {
    text-align: left;
    font-size: 14px;
  }

  .ant-pagination {
    float: right;
    margin-top: 10px !important;
  }
</style>
