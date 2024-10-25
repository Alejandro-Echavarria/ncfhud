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

const props = defineProps({
    data: Object,
    filter: String,
    page: String
});

const title = ref('');
const modal = ref(false);
const operation = ref(1);
const client = ref(null);

const form = useForm({
    rnc: '',
    business_name: '',
    commercial_activity: '',
    email: '',
});

const save = () => {
    if (operation.value === 1) {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).post(route('admin.clients.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                ok('Client created');
            },
            onError: () => {
                console.log("error");
            }
        });
    } else {
        form.transform((data) => ({
            ...data,
            search: props.filter,
            page: props.page
        })).post(route('admin.clients.update', client.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                ok('Client updated');
            },
            onError: () => {
                console.log("error");
            }
        });
    }
};

const openModal = (op, id, rnc, business_name, commercial_activity, email) => {
    modal.value = true;
    operation.value = op;

    if (op === 1) {
        title.value = 'Crea un nuevo cliente';
    } else {
        title.value = 'Editar cliente';
        client.value = id;
        form.rnc = rnc;
        form.business_name = business_name;
        form.commercial_activity = commercial_activity;
        form.email = email;
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

defineExpose({openModal});
</script>

<template>
    <div>
        <PrimaryButton class="w-full" @click="openModal(1)">
            <font-awesome-icon class="mr-2" :icon="['fas', 'plus']"/>
            Agregar cliente
        </PrimaryButton>

        <DialogModal :show="modal" :maxWidth="'4xl'" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <SimpleForm :actions="true" @submitted="save">
                    <template #form>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2">
                                <InputLabel for="rnc" value="RNC" :required="true"/>
                                <TextInput id="rnc" ref="rncInput" v-model="form.rnc" type="text"/>

                                <InputError :message="form.errors.rnc" class="mt-2"/>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="business_name" value="Nombre" :required="true"/>
                                <TextInput id="business_name" ref="businessNameInput" v-model="form.business_name"
                                           type="text"/>

                                <InputError :message="form.errors.business_name" class="mt-2"/>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="commercial_activity" value="Actividad comercial" :required="true"/>
                                <TextInput id="commercial_activity" ref="commercialActivityInput"
                                           v-model="form.commercial_activity" type="text"/>

                                <InputError :message="form.errors.commercial_activity" class="mt-2"/>
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="email" value="Email" :required="true"/>
                                <TextInput id="email" ref="EmailInput" v-model="form.email" type="text"/>

                                <InputError :message="form.errors.email" class="mt-2"/>
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
