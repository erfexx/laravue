<style>
.widget-user-header{
    background-position: center center;
    background-size: cover;
    height: 250px;
}
</style>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background-image: url('./img/cover.jpg')">
                        <h3 class="widget-user-username">Elizabeth Pierce</h3>
                        <h5 class="widget-user-desc">Web Designer</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" src="" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="settings">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Name</label>
                                        <div class="col-sm-12">
                                            <input type="text" v-model="form.name" class="form-control" name="name" id="inputName" placeholder="Name">
                                            <has-error :form="form" field="name"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                                        <div class="col-sm-12">
                                            <input type="email" name="email" v-model="form.email"
                                                   class="form-control" id="inputEmail"
                                                   placeholder="Email"
                                                   :class="{'is-invalid': form.errors.has('email')}"
                                            >
                                            <has-error :form="form" field="email"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="bio" class="col-sm-2 control-label">Bio</label>
                                        <div class="col-sm-12">
                                            <textarea class="form-control" name="bio" v-model="form.bio" id="bio" placeholder=""></textarea>
                                            <has-error :form="form" field="bio"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="profilePhoto" class="col-sm-2 control-label">Profile Photo</label>
                                        <div class="col-sm-12">
                                            <input type="file" @change="updateProfile"  class="form-control" id="profilePhoto" placeholder="Skills">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="col-sm-2 control-label">Password</label>
                                        <div class="col-sm-12">
                                            <input type="password" v-model="form.password" name="password" class="form-control" id="password" placeholder="">
                                            <has-error :form="form" field="password"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" @click.prevent="updateInfo" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
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
        mounted() {
            console.log('Component mounted.')
        },
        methods:{
            updateInfo(){
                this.$Progress.start();
                this.form.put('api/profile')
                    .then(() => {

                        this.$Progress.finish();
                    })
                    .catch(() => {

                        this.$Progress.fail();
                    });
            },

            updateProfile(e){
                console.log('uploading...');
                let file = e.target.files[0];
                let reader = new FileReader();

                if(file['size'] < 1000000){
                    reader.onloadend = (file) => {
                        // console.log('RESULT ', reader.result)
                        this.form.photo = reader.result;
                    };
                    reader.readAsDataURL(file);

                    toast.fire({
                        type: 'success',
                        title: 'Photo accepted'
                    });
                }
                else{
                    toast.fire({
                        type: 'error',
                        title: 'File is too big'
                    });
                }
            }
        },
        created() {
            axios.get("api/profile").then(({data}) => (this.form.fill(data)));
        }
    }
</script>
