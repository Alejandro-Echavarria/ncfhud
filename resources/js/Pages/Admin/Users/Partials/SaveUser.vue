<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SimpleForm from '@/Components/Main/Admin/Components/Forms/Forms/SimpleForm.vue';
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
import ToggleSwitch from "@/Components/Main/Admin/Components/Forms/Inputs/ToggleSwitch/ToggleSwitch.vue";
import VueSelect from "@/Components/Main/Admin/Components/Selects/VueSelect.vue";
import SaveAlert from "@/Helpers/Alerts/SaveAlert.js";
import { usePermission } from "@/Composables/permissions.js";

const props = defineProps({
    data: Object,
    filter: String,
    page: String
});

const title = ref('');
const modal = ref(false);
const closeOpenModal = ref(true);
const operation = ref(1);
const user = ref(null);
const rolesOptions = ref(props.data.roles);

const { hasPermission } = usePermission();
const canCreateUser = hasPermission("admin.users.create");

const PrimaryButtonComponent = canCreateUser ? PrimaryButton : null;

const form = useForm({
    name: '',
    email: '',
    password: '',
    is_active: true,
    roles: []
});

const save = () => {
    if (operation.value === 1) {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).post(route('admin.users.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                closeOpenModal.value = true;
                ok('Usuario creado');
            },
            onError: (errors) => {
                if (errors.create) {
                    closeOpenModal.value = false;
                    ok(errors.create, 'error', null, false, 'Error');
                }
            }
        });
    } else {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).put(route('admin.users.update', user.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                closeOpenModal.value = true;
                ok('Usuario actualizado');
            },
            onError: (errors) => {
                if (errors.update) {
                    closeOpenModal.value = false;
                    ok(errors.update, 'error', null, false, 'Error');
                }
            }
        });
    }
};

const openModal = (op, data) => {
    modal.value = true;
    operation.value = op;

    if (op === 1) {
        title.value = 'Crea un nuevo usuario';
    } else {
        title.value = 'Editar usuario';
        user.value = data.id;
        form.name = data.name;
        form.email = data.email;
        form.is_active = data.is_active;
        form.roles = data.roles.map(role => role.id);
        form.password = '';
    }
};

const closeModal = () => {
    modal.value = false;
    form.clearErrors();
    form.reset();
};

const ok = (msj, type, timer, toast, title) => {
    closeOpenModal.value && closeModal();
    SaveAlert(msj, type, timer, toast, title);
};

defineExpose({ openModal });
</script>

<template>
    <div>
        <component :is="PrimaryButtonComponent" ref="callOpenModal" class="w-full" @click="openModal(1)">
            <Icon icon="Plus" class="mr-1"/>
            Agregar usuario
        </component>

        <DialogModal :show="modal" :maxWidth="'4xl'" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <SimpleForm :actions="true" @submitted="save">
                    <template #form>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2">
                                <InputLabel for="name" value="Nombre" :required="true"/>
                                <TextInput id="name" ref="nameInput" v-model="form.name" type="text"/>

                                <InputError :message="form.errors.name" class="mt-2"/>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="role" value="Rol" :required="true"/>
                                <VueSelect id="role" label="name" v-model="form.roles"
                                           :multiple="true"
                                           :close-on-select="false"
                                           :append="true"
                                           :options="rolesOptions"
                                           :reduce="options => options.id"/>

                                <InputError :message="form.errors.roles" class="mt-2"/>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="email" value="Email" :required="true"/>
                                <TextInput id="email" ref="EmailInput" v-model="form.email" type="text"
                                           autocomplete="off"/>

                                <InputError :message="form.errors.email" class="mt-2"/>
                            </div>

                            <div class="flex items-center sm:col-span-2 gap-6">
                                <div class="grow">
                                    <InputLabel for="password" value="Password" :required="true"/>
                                    <TextInput id="password" ref="PasswordInput" v-model="form.password"
                                               type="password" autocomplete="off"/>

                                    <InputError :message="form.errors.password" class="mt-2"/>
                                </div>

                                <div>
                                    <InputLabel for="is_active" value="Estado"/>
                                    <ToggleSwitch id="is_active" ref="is_activeInput" v-model="form.is_active"
                                                  class="mt-2"/>

                                    <InputError :message="form.errors.is_active" class="mt-2"/>
                                </div>
                            </div>
                        </div>
                    </template>
                </SimpleForm>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal" class="mr-3">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton form="simpleForm" type="submit" @click="save" :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing">
                    Guardar
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
