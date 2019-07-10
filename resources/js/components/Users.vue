<template>
    <div class="container">
        <div class="content-header">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users Table</h3>

                            <div class="card-tools">
                                <button class="btn btn-success" @click="newModal">
                                    <i class="fas fa-user-plus fa-fw"></i>
                                    Add New
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Member since</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="user in users" :key="user.id">
                                    <td>{{user.id}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.email}}</td>
                                    <td><span class="tag tag-success">{{user.type | upText}}</span></td>
                                    <td><span class="tag tag-success">{{user.created_at | myDate}}</span></td>
                                    <td>
                                        <a href="#" class="text-blue" @click="editModal(user)">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        /

                                        <a href="#" class="text-red" @click="deleteUser(user.id)">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade"
             id="addNew"
             tabindex="-1"
             role="dialog"
             aria-labelledby="addNew"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editmode" class="modal-title" id="addNewUserTitle">Add New User</h5>
                        <h5 v-show="editmode" class="modal-title" id="updateUserTitle">Update User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" type="password" name="password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" type="email" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea
                                        class="form-control"
                                        v-model="form.bio"
                                        name="bio"
                                        id="bio"
                                        :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select
                                        class="form-control"
                                        v-model="form.type"
                                        name="type"
                                        id="type"
                                        :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard User Role</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Save User</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            updateUser(id){
                // console.log('dadada');
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id).then(() => {
                    Fire.$emit('AfterCreate');

                    $('#addNew').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'User updated successfully'
                    });
                    this.$Progress.finish();
                }).catch(() => {
                    console.log('wow');

                    $('#addNew').modal('hide');
                    toast.fire({
                        type: 'error',
                        title: 'Something went wrong'
                    });

                    this.$Progress.fail();
                });
            },
            deleteUser(id){
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value){
                        this.form.delete('api/user/'+id).then(() => {
                            swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            Fire.$emit('AfterCreate');
                        }).catch(() => {
                            swal("Failed!", "There was something wrong", "warning");
                        });
                    }
                })
            },
            loadUsers(){
                axios.get("api/user").then(({data}) => (this.users = data.data));
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/user').then(()=>{
                    Fire.$emit('AfterCreate');

                    $('#addNew').modal('hide');
                    toast.fire({
                        type: 'success',
                        title: 'User created successfully'
                    });
                    this.$Progress.finish();
                }).catch(() => {

                });
            }
        },
        created() {
            this.loadUsers();
            Fire.$on('AfterCreate', () => this.loadUsers());
            // setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>
