<script setup lang="ts">
import axios from 'axios';
import routes from '../config/routes';
import { onMounted, ref } from 'vue';

const customers       = ref([]);
const showEditModal   = ref(false);
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const errorOccurred   = ref(false);
const errorMsg        = ref(false);
const successOccurred = ref(false);
const successMsg      = ref(false);
const toDeleteId      = ref(0);
const toEditId        = ref(0);
onMounted(() => {
    document.addEventListener('keyup', (e) => {
        if (e.key === 'Escape') {
            closeShowEditModal();
            closeDeleteModal();
        }
    });
    getCustomers();
});
const userForm            = ref(
    {
        first_name:    '',
        last_name:     '',
        username:      '',
        date_of_birth: '',
        password:      '',
    });
const resetForm           = () => {
    userForm.value.first_name    = '';
    userForm.value.last_name     = '';
    userForm.value.username      = '';
    userForm.value.date_of_birth = '';
    userForm.value.password      = '';
};
const showCreateUserModal = () => {
    showCreateModal.value = true;
    toEditId.value        = 0;
    resetForm();
};
const showEditUserModal   = (id: number) => {
    showEditModal.value          = true;
    toEditId.value               = id;
    const customer               = customers.value.find((customer) => customer.id === id);
    userForm.value.first_name    = customer?.first_name ?? '';
    userForm.value.last_name     = customer?.last_name ?? '';
    userForm.value.username      = customer?.username ?? '';
    userForm.value.date_of_birth = customer?.date_of_birth ?? '';
    userForm.value.password      = '';
};
const closeShowEditModal  = () => {
    showCreateModal.value = false;
    showEditModal.value   = false;
    toEditId.value        = 0;
    resetForm();
};
const showUserDeleteModal = (id: number) => {
    showDeleteModal.value = true;
    toDeleteId.value      = id;
};
const closeDeleteModal    = () => {
    showDeleteModal.value = false;
    toDeleteId.value      = 0;
};
const handleFormSubmit    = () => {
    if (toEditId.value !== 0) {
        updateCustomer(toEditId.value);
    } else {
        createCustomer();
    }
};
const getCustomers        = () => {
    axios.get(routes.CUSTOMERS)
         .then((response) => {
             customers.value = response.data;
         })
         .catch((error) => {
             showError(error.response.data);
         });
};
const deleteCustomer      = (id: number) => {
    axios.delete(routes.CUSTOMER_DELETE + '/' + id)
         .then((response) => {
             closeDeleteModal();
             getCustomers();
             showSuccess('Customer deleted successfully');
         })
         .catch((error) => {
             showError(error.response.data);
         });
};
const updateCustomer      = (id: number) => {
    const data = {
        id:            id,
        first_name:    userForm.value.first_name,
        last_name:     userForm.value.last_name,
        username:      userForm.value.username,
        date_of_birth: userForm.value.date_of_birth,
        password:      userForm.value.password,
    };
    axios.post(routes.CUSTOMER_UPDATE + '/' + id, data, {
        headers: {
            'Content-Type': 'application/json',
        },
    })
         .then((response) => {
             getCustomers();
             resetForm();
             showSuccess('Customer updated successfully');
             closeShowEditModal();
         })
         .catch((error) => {
             showError(error.response.data);
         });
};
const createCustomer      = () => {
    const data = {
        first_name:    userForm.value.first_name,
        last_name:     userForm.value.last_name,
        username:      userForm.value.username,
        date_of_birth: userForm.value.date_of_birth,
        password:      userForm.value.password,
    };
    axios.post(routes.CUSTOMER_CREATE, data, {
        headers: {
            'Content-Type': 'application/json',
        },
    })
         .then((response) => {
             getCustomers();
             resetForm();
             showSuccess('Customer created successfully');
             closeShowEditModal();
         })
         .catch((error) => {
             showError(error.response.data);
         });
};
const showError           = (error: any) => {
    errorOccurred.value = true;
    errorMsg.value      = error;
    setTimeout(() => {
        errorOccurred.value = false;
        errorMsg.value      = '';
    }, 15000);
};
const showSuccess         = (message: string) => {
    successOccurred.value = true;
    successMsg.value      = message;
    setTimeout(() => {
        successOccurred.value = false;
        successMsg.value      = '';
    }, 15000);
};
</script>

<template>
    <main class="p-20">
        <h1 class="pb-10 uppercase text-center">Customers</h1>
        <div class="w-full text-center mb-4">
            <button
                    @click="showCreateUserModal"
                    class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700
                        hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300
                        shadow-lg shadow-blue-500/50 font-medium rounded-lg text-sm px-5 py-2.5
                        text-center me-2 mb-2 transition-all duration-300"
            >
                Create Customer
            </button>
        </div>
        <table class="table-auto mx-auto text-left text-gray-700" v-if="customers.length > 0">
            <thead class="uppercase text-xs bg-gray-200">
            <tr>
                <th class="p-3">First Name</th>
                <th class="p-3">Last Name</th>
                <th class="p-3">Username</th>
                <th class="p-3">Date of birth</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody class="text-xs bg-gray-50">
            <tr v-for="(customer, idx) in customers" :key="idx">
                <input type="hidden" name="id" :value="customer.id"/>
                <td class="p-3">{{ customer.first_name ?? '' }}</td>
                <td class="p-3">{{ customer.last_name ?? '' }}</td>
                <td class="p-3">{{ customer.username ?? '' }}</td>
                <td class="p-3">{{ customer.date_of_birth ?? '' }}</td>
                <td class="p-3">
                    <button
                            @click="showEditUserModal(customer.id)"
                            type="button"
                            class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500
                                    hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300
                                    shadow-lg shadow-lime-500/50 font-medium rounded-lg text-sm px-5 py-2.5
                                    text-center me-2 mb-2 transition-all duration-300">
                        Edit
                    </button>
                    <button
                            @click="showUserDeleteModal(customer.id)"
                            type="button"
                            class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600
                            hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300
                            shadow-lg shadow-red-500/50  font-medium rounded-lg text-sm px-5 py-2.5
                            text-center me-2 mb-2 transition-all duration-300">
                        Delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </main>

    <div v-if="showCreateModal || showEditModal">
        <Teleport to="body">
            <div class="modal-bg">
                <div class="modal-content bg-orange-100 text-red-900">
                    <div class="w-full flex">
                        <button
                                @click="closeShowEditModal"
                                type="button"
                                class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium mx-auto"
                        >
                            Close
                        </button>
                    </div>
                    <form @submit.prevent="handleFormSubmit" class="flex flex-col">
                        <div class="w-full flex center m-2">
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <label for="first_name" class="mr-4">First Name</label>
                                <input id="first_name"
                                       type="text"
                                       name="first_name"
                                       v-model="userForm.first_name"/>
                            </div>
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <label for="last_name" class="mr-4">Last Name</label>
                                <input id="last_name"
                                       type="text"
                                       name="last_name"
                                       v-model="userForm.last_name"/>
                            </div>
                        </div>

                        <div class="w-full flex center m-2">
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <label for="username" class="mr-4">Username</label>
                                <input id="username"
                                       type="text"
                                       name="username"
                                       v-model="userForm.username"/>
                            </div>
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <label for="password" class="mr-4">Password</label>
                                <input id="password"
                                       type="password"
                                       name="password"
                                       v-model="userForm.password"/>
                            </div>
                        </div>

                        <div class="w-full flex center m-2">
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <label for="date_of_birth" class="mr-4">Date of birth</label>
                                <input id="date_of_birth"
                                       type="date"
                                       name="date_of_birth"
                                       v-model="userForm.date_of_birth"/>
                            </div>
                        </div>
                        <div class="w-full flex center m-2">
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <button
                                        type="submit"
                                        class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium mx-auto"
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>

    <div v-if="showDeleteModal">
        <Teleport to="body">
            <div class="modal-bg">
                <div class="modal-content bg-orange-100 text-red-900">
                    <h4>
                        Are you sure you want to delete this customer?
                    </h4>
                    <div class="flex flex-row justify-around mt-12">
                        <button
                                @click="deleteCustomer(toDeleteId)"
                                type="button"
                                class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600
                            hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300
                            shadow-lg shadow-red-500/50  font-medium rounded-lg text-sm px-5 py-2.5
                            text-center me-2 mb-2">
                            Delete
                        </button>
                        <button
                                @click="closeDeleteModal"
                                type="button"
                                class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500
                                    hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300
                                    shadow-lg shadow-lime-500/50 font-medium rounded-lg text-sm px-5 py-2.5
                                    text-center me-2 mb-2">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>

    <div v-if="errorOccurred">
        <Teleport to="body">
            <div class="error">
                <div class="bg-red-600 text-red-50 p-10">
                    <h4>
                        {{ errorMsg }}
                    </h4>
                </div>
            </div>
        </Teleport>
    </div>

    <div v-if="successOccurred">
        <Teleport to="body">
            <div class="error">
                <div class="bg-green-600 text-green-50 p-10">
                    <h4>
                        {{ successMsg }}
                    </h4>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.modal-bg {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-radius: 0.2rem;
    padding: 6rem;
    text-align: center;
}

.error {
    position: fixed;
    bottom: 0;
    right: 0;
    width: 40%;
    z-index: 99999;
}
</style>
