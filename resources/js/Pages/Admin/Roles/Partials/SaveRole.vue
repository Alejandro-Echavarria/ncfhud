<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SimpleForm from "@/Components/Main/Admin/Components/Forms/Forms/SimpleForm.vue";
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SaveAlert from "@/Helpers/Alerts/SaveAlert.js";
import { usePermission } from "@/Composables/permissions.js";

const props = defineProps({
    data: Object,
    permissions: Object,
    filter: String,
    page: String
});

const title = ref('');
const modal = ref(false);
const closeOpenModal = ref(true);
const operation = ref(1);
const role = ref(null);

const { hasPermission } = usePermission();
const canCreateRole = hasPermission("admin.roles.create");

const PrimaryButtonComponent = canCreateRole ? PrimaryButton : null;

const form = useForm({
    name: '',
    permissions: [],
});

const save = () => {
    if (operation.value === 1) {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).post(route('admin.roles.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                closeOpenModal.value = true;
                ok('Rol creado');
            },
            onError: (errors) => {
                if (errors.update) {
                    closeOpenModal.value = false;
                    ok(errors.update, 'error', null, false, 'Error');
                }
            }
        });
    } else {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).put(route('admin.roles.update', role.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                closeOpenModal.value = true;
                ok('Rol actualizado');
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
        title.value = 'Crea un nuevo role';
    } else {
        title.value = 'Editar role';
        role.value = data.id;
        form.name = data.name;
        form.permissions = data.permissions.map(permission => permission.id);
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
            Agregar role
        </component>

        <DialogModal :show="modal" :maxWidth="'4xl'" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <SimpleForm :actions="true" @submitted="save">
                    <template #form>
                        <div class="grid grid-cols-4 gap-6">
                            <div class="col-span-4">
                                <InputLabel for="name" value="Nombre" :required="true"/>
                                <TextInput id="name" ref="nameInput" v-model="form.name" type="text"/>

                                <InputError :message="form.errors.name" class="mt-2"/>
                            </div>

                            <div class="col-span-4">
                                <span class="mb-2 block font-medium text-sm text-gray-700 dark:text-gray-300">
                                    Roles
                                    <span class="text-red-500"> * </span>
                                </span>

                                <div v-for="(permissionsByType, type) in permissions" :key="type" class="mb-8">
                                    <!-- Título del tipo de permisos -->
                                    <div class="my-4 capitalize flex gap-1">
                                        <p class="text-sm font-medium">
                                            {{ type.replace('_', ' ') }}
                                        </p>
                                    </div>

                                    <!-- Permisos del tipo -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div v-for="permission in permissionsByType" :key="permission.id"
                                             class="flex gap-3">
                                            <Checkbox v-model:checked="form.permissions" :value="permission.id"
                                                      :id="`role-${permission.name}`"/>

                                            <InputLabel class="font-normal" :for="`role-${permission.name}`"
                                                        :value="permission.description"
                                                        :required="false"/>
                                        </div>
                                    </div>
                                </div>

                                <!-- Error de permisos -->
                                <InputError :message="form.errors.permissions" class="mt-2"/>
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
