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
import ToggleSwitch from "@/Components/Main/Admin/Components/Forms/Inputs/ToggleSwitch/ToggleSwitch.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    data: Object,
    permissions: Object,
    filter: String,
    page: String
});

const title = ref('');
const modal = ref(false);
const operation = ref(1);
const role = ref(null);

const form = useForm({
    name: '',
    permissions: [],
});

const openModal = (op, data) => {
    modal.value = true;
    operation.value = op;

    if (op === 1) {
        title.value = 'Crea un nuevo role';
    } else {
        title.value = 'Editar role';
        role.value = data.id;
        form.name = data.name;
        form.email = data.email;
        form.is_active = data.is_active;
        form.password = '';
    }
};

const closeModal = () => {
    modal.value = false;
    form.clearErrors();
    form.reset();
};

const ok = (msj, type, timer) => {
    closeModal();
    // SaveAlert(msj, type, timer);
};

defineExpose({ openModal });
</script>

<template>
    <div>
        <PrimaryButton class="w-full" @click="openModal(1)">
            <Icon icon="Plus" class="mr-1"/>
            Agregar role
        </PrimaryButton>

        <DialogModal :show="modal" :maxWidth="'4xl'" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <SimpleForm :actions="true" @submitted="save">
                    <template #form>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-4">
                                <InputLabel for="name" value="Nombre" :required="true"/>
                                <TextInput id="name" ref="nameInput" v-model="form.name" type="text"/>

                                <InputError :message="form.errors.name" class="mt-2"/>
                            </div>

                            <div class="col-span-4">
                                <InputLabel for="roles" value="Roles" :required="false"/>
                                <div class="grid grid-cols-2 gap-3">
                                    <div v-for="permission in permissions" :key="permission.id">
                                        <Checkbox v-model="form.permissions" :value="permission.id"/>
                                        <span class="ml-2 text-sm text-gray-600">{{ permission.description }}</span>

                                        <InputError :message="form.errors.roles" class="mt-2"/>
                                    </div>
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

<style scoped>

</style>