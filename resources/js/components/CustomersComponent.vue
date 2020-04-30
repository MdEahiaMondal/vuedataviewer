<template>
    <div id="customer">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Customers
                        <p class="float-right">
                            <button @click="ShowAddModal()" class="btn btn-sm btn-primary">Add New <i class="fa fa-plus"></i></button>
                            <button @click="freshData()" class="btn btn-sm btn-primary">Reload</button>
                        </p>
                    </div>

                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <h4>Search By:</h4>
                            </div>
                            <div class="col-md-3">
                                <select name="field" v-model="field" class="form-control" id="">
                                    <option value="name">Name</option>
                                    <option value="email">Email</option>
                                    <option value="phone">phone</option>
                                    <option value="address">Address</option>
                                    <option value="total">Total</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" v-model="query" placeholder="Search..." class="form-control">
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="customers.length" v-for="(customer, index) in customers" :key="customer.id">
                                        <th scope="row">{{ index }}</th>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.email }}</td>
                                        <td>{{ customer.mobile }}</td>
                                        <td>{{ customer.total }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>
                                            <button class="btn btn-primary btn-sm" @click="editModalShow(customer)"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-danger btn-sm" @click="destroy(customer)"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <tr v-if="!customers.length">
                                    <td colspan="6"><div class="alert alert-danger text-center">No data Found</div></td>
                                </tr>
                                </tbody>
                            </table>
                            <paginate-component
                                v-if="pagination.last_page > 1"
                                :pagination="pagination"
                                :offset="5"
                                @paginate="query === ''? fetchCustomers(): searchCustomer()"
                            ></paginate-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="customerModalCenter" tabindex="-1" role="dialog" aria-labelledby="customerModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customerModalCenterTitle">{{ editableMode ? 'Edit ' : 'Add New ' }} Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent=" editableMode ? updateData(): storeNewData()" @keydown="form.onKeydown($event)">
                    <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input v-model="form.email" type="email" name="email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input v-model="form.mobile" type="text" name="mobile"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('mobile') }">
                                <has-error :form="form" field="mobile"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea v-model="form.address" class="form-control"
                                          :class="{ 'is-invalid': form.errors.has('address') }"
                                          name="address" id="address" cols="30" rows="10">
                                </textarea>
                                <has-error :form="form" field="address"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Total</label>
                                <input v-model="form.total" type="text" name="total"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('total') }">
                                <has-error :form="form" field="total"></has-error>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" :disabled="form.busy"  class="btn btn-primary" >Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <vue-progress-bar></vue-progress-bar>
        <vue-snotify></vue-snotify>
    </div>
</template>

<script>
    /*this.form === it is a pakege function*/

    export default {
        data() {
            return {
                editableMode: false,
                field: 'name',
                query: '',
                customers: [],
                pagination: {
                    current_page: 1
                },
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    mobile: '',
                    address: '',
                    total: '',
                })
            }
        },
       watch: {

            query(newQuery, oldQuery)
            {
                if (newQuery === '')
                {
                    this.fetchCustomers()
                }else{
                    this.searchCustomer();
                }
            },
       },
        mounted() {
            console.log('Customer Component mounted.')
            this.fetchCustomers();
        },
        methods: {
            fetchCustomers()
            {
                this.$Progress.start();
                axios.get('/api/customer?page='+this.pagination.current_page)
                .then(res => {
                    this.customers = res.data.data;
                    this.pagination = res.data.meta;
                    this.$Progress.finish();
                })
                .catch(error => {
                    console.log(error.response);
                    this.$Progress.fail();
                })
            },
            searchCustomer()
            {
                this.$Progress.start();
                axios.get('/api/search/customer/'+this.field+'/'+this.query+'?page='+this.pagination.current_page)
                .then(res => {
                    console.log(res)
                    this.customers = res.data.data;
                    this.pagination = res.data.meta;
                    this.$Progress.finish();
                })
                .catch(error => {
                    console.log(error.response);
                    this.$Progress.fail();
                })
            },
            freshData()
            {
                this.fetchCustomers()
                this.query = '';
                this.field = 'name';
                this.$snotify.success('Page reloaded success', 'Success');
            },
            ShowAddModal()
            {
                this.editableMode = false;
                this.form.reset();
                this.form.clear();
                $('#customerModalCenter').modal('show');
            },
            storeNewData()
            {
                this.$Progress.start();
                this.form.busy = true; // only one to submit
                // Submit the form via a POST request
                this.form.post('/api/customer')
                .then(({ data }) => {
                    this.fetchCustomers()
                    $('#customerModalCenter').modal('hide');
                    if(this.form.successful)
                    {
                        this.$Progress.finish();
                        this.$snotify.success('New Item created done', 'Success');
                    }else{
                        this.$Progress.fail();
                        this.$snotify.error('Something went wrong please try again letter', 'Error');
                    }
                })
                .catch(err => {
                    console.log(err.response)
                    this.$Progress.fail();
                })
            },
            editModalShow(customer)
            {
                this.form.reset(); // reset maine's all data will be clear
                this.form.clear() // all form error will be clear
                this.editableMode = true;
                this.form.fill(customer); // this is a form method, automatically  fill all field with require
                $('#customerModalCenter').modal('show');
            },
            updateData()
            {
                this.$Progress.start();
                this.form.busy = true; // only one to submit
                // Submit the form via a POST request
                this.form.put('/api/customer/'+this.form.id)
                    .then(({ data }) => {
                        this.fetchCustomers()
                        $('#customerModalCenter').modal('hide');
                        if(this.form.successful)
                        {
                            this.$Progress.finish();
                            this.$snotify.success('Updated Success', 'Success');
                        }else{
                            this.$Progress.fail();
                            this.$snotify.error('Something went wrong please try again letter', 'Error');
                        }
                    })
                    .catch(err => {
                        console.log(err.response)
                        this.$Progress.fail();
                    })
            },
            destroy(customer)
            {
                this.$snotify.clear();
                this.$snotify.confirm('Are you sure want to delete this ?', 'Delete Permission!', {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: false,
                    pauseOnHover: true,
                    buttons: [
                        {
                            text: 'Yes', action: (toast) => {
                                this.$Progress.start();
                            axios.delete('/api/customer/'+customer.id)
                                .then(res => {
                                    this.fetchCustomers()
                                    this.$snotify.remove(toast.id);
                                    this.$Progress.finish();
                                    this.$snotify.success('Deleted Success', 'Success');
                                })
                                .catch(error => {
                                    this.$Progress.fail();
                                    console.log(error.response)
                                })
                            }
                        },
                        {
                            text: 'No', action: (toast) => {
                                this.$snotify.remove(toast.id);
                            },
                            bold: true
                        },
                    ]
                });
            }
        }
    }
</script>
