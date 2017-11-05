<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Status Kepegawaian
            </div>
            <div class="card-body">
                <table class="table table-striped" id="data">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th class="text-right">
                                <a class="btn btn-primary" v-on:click="action('add',0)">
                                    <i class="fa fa-plus"></i>
                                    Add
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(p,key,index) in tableData">
                            <td>{{key+1}}</td>
                            <td>{{p.nama_status}}</td>
                            <td class="text-right">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-warning" v-on:click="action('edit',p.id)">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn-danger" v-on:click="hapus(p.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Large modal -->
        <div id="modal_large" class="modal fade" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">{{title}}</h5>
                    </div>

                    <div class="modal-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="text" v-model="form.title" class="form-control" name="title" placeholder="Title" v-on:change="changeSlug">
                            </div>

                            <!--
                            <div class="form-group">
                                <label for="">Slug</label>
                                <input type="text" v-model="form.slug" class="form-control" name="Slug" placeholder="Slug">
                            </div>
                            -->

                            <div class="form-group">
                                <label for="">Description</label>
                                <div id="desc" data-no-turbolink>
                                    <ckeditor v-model="form.desc" :config="config"></ckeditor>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Image</label>
                                <img :src="form.image" class="img-responsive">
                                <img :src="form.imageEdit" class="img-responsive">
                                <input type="file" v-on:change="onFileChange" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" v-on:click="simpan()">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /large modal -->


    </div>
</template>

<script>
    export default {
        data(){
            return{
                form:{},
                tableData:{},
            }
        },
        mounted() {
            this.showData();
        },
        methods:{
            showData(){
                axios.get('home/data/status').then(resp => {
                    console.log(resp);
                    this.tableData=resp.data.data;
                }).then(()=>{
                    $("#data").DataTable({
                        "paging": true,
                        "ordering": false,
                        "info": true,
                        "autoWidth": false,
                        "destroy": true,
                    });
                });
            },

            action(type,id){
                this.type=type;
                this.kode=id;

                switch(type){
                    case 'add':
                            this.title="Add New Category";
                        break;
                    case 'edit':
                            this.title="Edit Category";
                            // axios.get('home/data/category/'+id)
                            //     .then(response=>{
                            //         this.form={
                            //             'title':response.data.title,
                            //             'slug':response.data.slug,
                            //             'desc':response.data.content,
                            //             'imageEdit':'uploads/category/'+response.data.featured_image
                            //         }
                            //     })
                        break;
                }

                $("#modal_large").modal('show');           
            },
            hapus(id){
                // this.$swal({
                //     title: 'Error!',
                //     text: 'Do you want to continue',
                //     type: 'error',
                //     confirmButtonText: 'Cool'
                // })

                // this.$swal({
                //     title: 'Are you sure?',
                //     text: "You won't be able to revert this!",
                //     type: 'warning',
                //     showCancelButton: true,
                //     confirmButtonColor: '#3085d6',
                //     cancelButtonColor: '#d33',
                //     confirmButtonText: 'Yes, delete it!'
                // }).then(function () {
                //     axios.delete('home/data/category/'+id)
                //         .then(resp=>{
                //             this.getCategorys();
                //             this.$swal(
                //                 'Deleted!',
                //                 'Your file has been deleted.',
                //                 'success'
                //             )
                //         })
                // })
            }
        }
    }
</script>
