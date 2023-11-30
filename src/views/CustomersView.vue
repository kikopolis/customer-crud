<script setup lang="ts">
import axios from 'axios';
import routes from '../config/routes';
import { onMounted, ref } from 'vue';

const customers       = ref([]);
const formClean       = ref(false);
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
const userForm                  = ref(
    {
        first_name:    '',
        last_name:     '',
        username:      '',
        date_of_birth: '',
        password:      '',
    },
);
const formErrors                = ref(
    {
        first_name:    '',
        last_name:     '',
        username:      '',
        date_of_birth: '',
        password:      '',
    });
const validateFirstName         = () => {
    const error = validateName(userForm.value.first_name);
    if (error && formErrors.value.first_name !== error) {
        formErrors.value.first_name = 'First name' + error;
    } else {
        if (!error && formErrors.value.first_name !== '') {
            formErrors.value.first_name = '';
        }
    }
};
const validateLastName          = () => {
    const error = validateName(userForm.value.last_name);
    if (error && formErrors.value.last_name !== error) {
        formErrors.value.last_name = 'Last name' + error;
    } else {
        if (!error && formErrors.value.last_name !== '') {
            formErrors.value.last_name = '';
        }
    }
};
const validateName              = (name: string) => {
    if (name.length < 3 && name.length > 0) {
        return ' must be at least 3 characters long';
    }
    if (name.length > 255) {
        return ' must be less than 255 characters long';
    }
    if (name.length === 0) {
        return ' is required';
    }
    if (name && name.match(/[^a-zA-Z0-9 ]/) && name.length > 0) {
        return ' must contain only letters, numbers and spaces';
    }
    formClean.value = canSubmit();
    return null;
};
const validateUsername          = () => {
    const error = validateUsernameFormat(userForm.value.username);
    if (error && formErrors.value.username !== error) {
        formErrors.value.username = 'Username' + error;
    } else {
        if (!error && formErrors.value.username !== '') {
            formErrors.value.username = '';
        }
    }
};
const validateUsernameFormat    = (username: string) => {
    if (username.length < 3 && username.length > 0) {
        return ' must be at least 3 characters long';
    }
    if (username.length > 255) {
        return ' must be less than 255 characters long';
    }
    if (username.length === 0) {
        return ' is required';
    }
    if (username && username.match(/[^a-zA-Z0-9]/) && username.length > 0) {
        return ' must contain only letters and numbers';
    }
    formClean.value = canSubmit();
    return null;
};
const validatePassword          = () => {
    if (toEditId.value !== 0 && userForm.value.password === '') {
        formErrors.value.password = '';
        formClean.value = canSubmit();
        return;
    }
    const error = validatePasswordFormat(userForm.value.password);
    if (error && formErrors.value.password !== error) {
        formErrors.value.password = 'Password' + error;
    } else {
        if (!error && formErrors.value.password !== '') {
            formErrors.value.password = '';
        }
    }
};
const validatePasswordFormat    = (password: string) => {
    if (password === '') {
        return ' is required';
    }
    if (password.length < 8 && password.length > 0) {
        return ' must be at least 8 characters long';
    }
    if (password && !password.match(/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/)) {
        return ' must contain at least one lowercase letter, one uppercase letter and one number';
    }
    formClean.value = canSubmit();
    return null;
};
const validateDateOfBirth       = () => {
    const error = validateDateOfBirthFormat(userForm.value.date_of_birth);
    if (error && formErrors.value.date_of_birth !== error) {
        formErrors.value.date_of_birth = 'Date of birth' + error;
    } else {
        if (!error && formErrors.value.date_of_birth !== '') {
            formErrors.value.date_of_birth = '';
        }
    }
};
const validateDateOfBirthFormat = (dateOfBirth: string) => {
    if (dateOfBirth.length === 0) {
        return ' is required';
    }
    if (dateOfBirth && dateOfBirth.match(/[^0-9-]/) && dateOfBirth.length > 0) {
        return ' must be in the format YYYY-MM-DD';
    }
    formClean.value = canSubmit();
    return null;
};
const canSubmit                 = () => {
    return formErrors.value.first_name === ''
           && formErrors.value.last_name === ''
           && formErrors.value.username === ''
           && formErrors.value.date_of_birth === ''
           && formErrors.value.password === ''
           && userForm.value.first_name !== ''
           && userForm.value.last_name !== ''
           && userForm.value.username !== ''
           && userForm.value.date_of_birth !== ''
           && (userForm.value.password !== '' || toEditId.value !== 0);
};
const validateForm              = () => {
    validateFirstName();
    validateLastName();
    validateUsername();
    validatePassword();
    validateDateOfBirth();
    formClean.value = canSubmit();
};
const resetForm                 = () => {
    userForm.value.first_name    = '';
    userForm.value.last_name     = '';
    userForm.value.username      = '';
    userForm.value.date_of_birth = '';
    userForm.value.password      = '';
};
const showCreateUserModal       = () => {
    showCreateModal.value = true;
    toEditId.value        = 0;
    resetForm();
};
const showEditUserModal         = (id: number) => {
    showEditModal.value          = true;
    toEditId.value               = id;
    const customer               = customers.value.find((customer) => customer.id === id);
    userForm.value.first_name    = customer?.first_name ?? '';
    userForm.value.last_name     = customer?.last_name ?? '';
    userForm.value.username      = customer?.username ?? '';
    userForm.value.date_of_birth = customer?.date_of_birth ?? '';
    userForm.value.password      = '';
    canSubmit();
};
const closeShowEditModal        = () => {
    showCreateModal.value = false;
    showEditModal.value   = false;
    toEditId.value        = 0;
    resetForm();
};
const showUserDeleteModal       = (id: number) => {
    showDeleteModal.value = true;
    toDeleteId.value      = id;
};
const closeDeleteModal          = () => {
    showDeleteModal.value = false;
    toDeleteId.value      = 0;
};
const handleFormSubmit          = () => {
    if (toEditId.value !== 0) {
        updateCustomer(toEditId.value);
    } else {
        createCustomer();
    }
};
const getCustomers              = () => {
    axios.get(routes.CUSTOMERS)
         .then((response) => {
             customers.value = response.data;
         })
         .catch((error) => {
             showError(error.response.data);
         });
};
const deleteCustomer            = (id: number) => {
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
const updateCustomer            = (id: number) => {
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
const createCustomer            = () => {
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
const showError                 = (error: any) => {
    errorOccurred.value = true;
    errorMsg.value      = error;
    setTimeout(() => {
        errorOccurred.value = false;
        errorMsg.value      = '';
    }, 15000);
};
const showSuccess               = (message: string) => {
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
                    <form @submit.prevent="handleFormSubmit"
                          class="flex flex-col"
                    >
                        <div class="w-full flex center m-2">
                            <div class="flex flex-col w-1/2">
                                <div class="flex flex-row w-full mx-auto p-3 items-center text-center">
                                    <label for="first_name" class="mr-4">First Name</label>
                                    <input id="first_name"
                                           type="text"
                                           name="first_name"
                                           :onblur="validateFirstName"
                                           :oninput="validateForm"
                                           v-model="userForm.first_name"/>
                                </div>
                                <div v-if="formErrors.first_name"
                                     class="mx-auto bg-red-600 text-gray-200 rounded-sm p-1 w-3/4">
                                    {{ formErrors.first_name }}
                                </div>
                            </div>
                            <div class="flex flex-col w-1/2">
                                <div class="flex flex-row w-full mx-auto p-3 items-center text-center">
                                    <label for="last_name" class="mr-4">Last Name</label>
                                    <input id="last_name"
                                           type="text"
                                           name="last_name"
                                           :onblur="validateLastName"
                                           :oninput="validateForm"
                                           v-model="userForm.last_name"/>
                                </div>
                                <div v-if="formErrors.last_name"
                                     class="mx-auto bg-red-600 text-gray-200 rounded-sm p-1 w-3/4">
                                    {{ formErrors.last_name }}
                                </div>
                            </div>
                        </div>


                        <div class="w-full flex center m-2">
                            <div class="flex flex-col w-1/2">
                                <div class="flex flex-row w-full mx-auto p-3 items-center text-center">
                                    <label for="username" class="mr-4">Username</label>
                                    <input id="username"
                                           type="text"
                                           name="username"
                                           :onblur="validateUsername"
                                           :oninput="validateForm"
                                           v-model="userForm.username"/>
                                </div>
                                <div v-if="formErrors.username"
                                     class="mx-auto bg-red-600 text-gray-200 rounded-sm p-1 w-3/4">
                                    {{ formErrors.username }}
                                </div>
                            </div>

                            <div class="flex flex-col w-1/2">
                                <div class="flex flex-row w-full mx-auto p-3 items-center text-center">
                                    <label for="password" class="mr-4">Password</label>
                                    <input id="password"
                                           type="password"
                                           name="password"
                                           :onblur="validatePassword"
                                           :oninput="validateForm"
                                           v-model="userForm.password"/>
                                </div>
                                <div v-if="formErrors.password"
                                     class="mx-auto bg-red-600 text-gray-200 rounded-sm p-1 w-3/4">
                                    {{ formErrors.password }}
                                </div>
                            </div>

                        </div>

                        <div class="w-full flex center m-2">
                            <div class="flex flex-col w-1/2">
                                <div class="flex flex-row w-full mx-auto p-3 items-center text-center">
                                    <label for="date_of_birth" class="mr-4">Date of birth</label>
                                    <input id="date_of_birth"
                                           type="date"
                                           name="date_of_birth"
                                           :onblur="validateDateOfBirth"
                                           :oninput="validateForm"
                                           v-model="userForm.date_of_birth"/>
                                </div>
                                <div v-if="formErrors.date_of_birth"
                                     class="mx-auto bg-red-600 text-gray-200 rounded-sm p-1 w-3/4">
                                    {{ formErrors.date_of_birth }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex center m-2">
                            <div class="flex flex-row w-1/2 mx-auto p-3 items-center text-center">
                                <button
                                        :disabled="!formClean"
                                        type="submit"
                                        class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium mx-auto
                                                transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
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
