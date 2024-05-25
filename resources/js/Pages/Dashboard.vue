<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Modal from "@/Components/Modal.vue";
import { Head, useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { component as ckeditor } from "@ckeditor/ckeditor5-vue";
import { inject, nextTick, ref } from "vue";

// Ive created a file named toast.js to implement Notyf and provided it to the vueApp so we can start with some default behavior and full availability
const toast = inject("toast");
const swal = inject("$swal");
const editor = ClassicEditor;

const editingTask = ref(false);
const creatingTask = ref(false);
const prop = defineProps({
    tasks: {
        type: Array,
    },
    statusList: {
        type: Object,
    },
    priorityList: {
        type: Object,
    },
});

let form;
// The form is created at the modal opening. The values in the form will be binded to the modal fields with v-model
const makeForm = (data) => {
    form = useForm({
        id: data.id ?? null,
        name: data.name ?? "",
        description: data.description ?? "",
        status: data.status ?? "",
        deadline: data.deadline ?? "",
        priority: data.priority ?? "",
        ownername: data.ownername,
    });
};

const submit = () => {
    editingTask.value
        ? form.patch(route("task.update"), {
              onSuccess: () => {
                  toast.success("Task updated successfully!");
                  closeModal();
              },
              preserveScroll: true,
              onError: (msg) => {
                  toast.errors(msg);
              },
          })
        : form.post(route("task.create"), {
              onFinish: () => {
                  toast.success("Task created successfully!");
                  closeModal();
              },
              preserveScroll: true,
              onError: (msg) => {
                  toast.errors(msg);
              },
          });
};

// If you want the modal to be filled with task information just pass a taskIndex (for editing or reading purposes)
const openModal = (taskIndex) => {
    makeForm(typeof taskIndex !== "undefined" ? prop.tasks[taskIndex] : {});

    typeof taskIndex !== "undefined"
        ? nextTick((editingTask.value = true))
        : nextTick((creatingTask.value = true));
};

const closeModal = () => {
    form.reset();
    editingTask.value = creatingTask.value = false;
};

const confirmPickUpTask = (taskId) => {
    swal.fire({
        title: "Are you sure?",
        text: "Do you want to take this task?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#1F2937",
        cancelButtonColor: "#EF4444CC",
        confirmButtonText: "YES, TAKE IT",
        cancelButtonText: "NO",
        background: "#f4f6f7",
        color: "#333",
        customClass: {
            popup: "neutral-popup",
            title: "neutral-title",
            confirmButton: "neutral-confirm-button",
            cancelButton: "neutral-cancel-button",
        },
    }).then((action) => {
        if (action.isConfirmed) {
            const form = useForm({});
            form.patch(route("task.take", { id: taskId }), {
                onFinish: () => {
                    toast.success("You have taken the task.");
                },
                onError: (msg) => {
                    toast.errors(msg);
                },
            });
        }
    });
};

const confirmTaskCompletion = (taskId) => {
    swal.fire({
        title: "Are you sure?",
        text: "Do you want to set the status of this task to 'Complete'?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#1F2937",
        cancelButtonColor: "#EF4444CC",
        confirmButtonText: "YES, FINISH IT",
        cancelButtonText: "NO",
        background: "#f4f6f7",
        color: "#333",
        customClass: {
            popup: "neutral-popup",
            title: "neutral-title",
            confirmButton: "neutral-confirm-button",
            cancelButton: "neutral-cancel-button",
        },
    }).then((action) => {
        if (action.isConfirmed) {
            const form = useForm({});
            form.patch(route("task.finish", { id: taskId }), {
                onFinish: () => {
                    toast.success("Task status set to Completed.");
                    closeModal();
                },
                onError: (msg) => {
                    toast.errors(msg);
                },
            });
        }
    });
};

// The form is created empty the first time in order for the screen to render properly
makeForm({});

// If I had more time, I would create a CrudModal.vue component and make it generic for the sake of standardization
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <section class="max-w-7xl mx-auto sm:px-6 lg:px-4 py-4 my-4 relative">
            <section
                class="my-6 w-full flex justify-between rounded-lg bg-white"
            >
                <div class="overflow-hidden shadow-sm">
                    <h2 class="p-6 text-gray-900">
                        Welcome {{ $page.props.auth.user.name }}
                    </h2>
                </div>
                <PrimaryButton @click="openModal()" class="my-4 mr-6">
                    New Task
                </PrimaryButton>
            </section>

            <section
                class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg"
            >
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 w-48"
                            >
                                Name
                            </th>
                            <th
                                scope="col"
                                class="px-3 text-left text-sm font-semibold text-gray-900"
                            >
                                Description
                            </th>
                            <th
                                scope="col"
                                class="px-3 text-left text-sm font-semibold text-gray-900 w-48"
                            >
                                Status
                            </th>
                            <th
                                scope="col"
                                class="relative px-4 sm:pl-6 w-16"
                            ></th>
                            <th
                                scope="col"
                                class="relative pr-4 pl-2 sm:px-6 w-16"
                            >
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="(task, i) in tasks" v-bind:key="i">
                            <td
                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 truncate text-ellipsis max-w-[100px]"
                            >
                                {{ task.name }}
                            </td>
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 truncate text-ellipsis max-w-[100px]"
                            >
                                {{
                                    task.description.replace(
                                        /<\/?[^>]+(>|$)/g,
                                        ""
                                    )
                                }}
                            </td>
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                            >
                                {{ task.status }}
                            </td>
                            <td
                                class="relative whitespace-nowrap text-sm font-medium text-slate-500 hover:text-slate-400 cursor-pointer"
                            >
                                <SecondaryButton
                                    type="button"
                                    @click="confirmPickUpTask(task.id)"
                                    class="hover:bg-slate-200 hover:bg-opacity-60"
                                >
                                    Pick task
                                </SecondaryButton>
                            </td>
                            <td
                                class="py-4 pl-3 pr-4 relative whitespace-nowrap text-sm font-medium sm:pr-6 text-slate-500 hover:text-slate-400 cursor-pointer"
                            >
                                <SecondaryButton
                                    type="button"
                                    @click="openModal(i)"
                                    class="hover:bg-slate-200 hover:bg-opacity-60"
                                >
                                    <v-icon name="hi-solid-pencil" />
                                </SecondaryButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="my-2">
                <div
                    @click="openModal()"
                    class="py-3 flex justify-center rounded-lg text-slate-400 hover:text-slate-500 hover:bg-slate-400 hover:bg-opacity-10 transition-all cursor-pointer"
                >
                    <v-icon name="bi-plus-circle"></v-icon>
                </div>
            </section>

            <Modal :show="editingTask || creatingTask" @close="closeModal">
                <div
                    class="mt-2 h-min bg-white overflow-hidden shadow-sm rounded-lg"
                >
                    <form @submit.prevent="submit">
                        <div class="py-2 pt-4 px-6">
                            <div class="flex justify-between">
                                <div class="w-full">
                                    <InputLabel for="name" value="Nome" />

                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.taskName"
                                    />
                                </div>

                                <div class="ml-3 relative w-60">
                                    <InputLabel for="status" value="Status" />
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md w-full"
                                            >
                                                <button
                                                    type="button"
                                                    class="mt-1 w-full inline-flex items-center px-3 py-3 border border-gray-300 border-1 shadow-sm text-sm leading-4 rounded-md text-gray-500 hover:text-gray-700 transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        statusList[
                                                            form.status
                                                        ] || "Select"
                                                    }}

                                                    <svg
                                                        class="absolute right-3 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                            <input
                                                type="hidden"
                                                id="status"
                                                name="status"
                                                v-model="form.status"
                                            />
                                        </template>

                                        <template #content>
                                            <button
                                                type="button"
                                                v-for="(
                                                    status, i
                                                ) in statusList"
                                                v-bind:key="i"
                                                @click="form.status = i"
                                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                            >
                                                {{ status }}
                                            </button>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <div class="mt-4">
                                <InputLabel
                                    for="description"
                                    value="Description"
                                />

                                <ckeditor
                                    id="description"
                                    :editor="editor"
                                    type="description"
                                    v-model="form.description"
                                    class="rounded-md"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password"
                                />
                            </div>

                            <div class="mt-4 flex justify-between">
                                <div class="w-fit">
                                    <InputLabel for="ownerName" value="Owner" />

                                    <TextInput
                                        id="ownerName"
                                        type="text"
                                        class="mt-[5px] block w-full"
                                        v-model="form.ownername"
                                        disabled
                                        autofocus
                                    />
                                </div>

                                <div class="ml-3 w-52">
                                    <InputLabel
                                        for="deadline"
                                        value="Deadline"
                                    />

                                    <VueDatePicker
                                        :teleport="true"
                                        class="mt-[5px]"
                                        date
                                        v-model="form.deadline"
                                        :format="
                                            (date) => {
                                                return date
                                                    ? `${date.getFullYear()}-${(
                                                          date.getMonth() + 1
                                                      )
                                                          .toString()
                                                          .padStart(
                                                              2,
                                                              '0'
                                                          )}-${date.getDate()}`
                                                    : null;
                                            }
                                        "
                                        :disabled="editingTask"
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.deadline"
                                    />
                                </div>

                                <div class="ml-3 relative w-64">
                                    <InputLabel
                                        for="priority"
                                        value="Priority"
                                    />
                                    <Dropdown align="bottom-right" width="48">
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md w-full"
                                            >
                                                <button
                                                    type="button"
                                                    class="mt-1 w-full inline-flex items-center px-3 py-3 border border-gray-300 border-1 shadow-sm text-sm leading-4 rounded-md text-gray-500 hover:text-gray-700 transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        priorityList[
                                                            form.priority
                                                        ] || "Select"
                                                    }}

                                                    <svg
                                                        class="absolute right-3 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                            <input
                                                type="hidden"
                                                id="priority"
                                                name="priority"
                                                v-model="form.priority"
                                            />
                                        </template>

                                        <template #content>
                                            <button
                                                type="button"
                                                v-for="(
                                                    priority, i
                                                ) in priorityList"
                                                v-bind:key="i"
                                                @click="form.priority = i"
                                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                            >
                                                {{ priority }}
                                            </button>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between py-4 px-6 mt-2 border-t-[1px]"
                        >
                            <div>
                                <SecondaryButton
                                    v-if="editingTask"
                                    type="button"
                                    @click="confirmTaskCompletion(form.id)"
                                    class="bg-green-300 hover:bg-green-500 hover:bg-opacity-80"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Finish Task
                                </SecondaryButton>
                            </div>

                            <div class="flex gap-2">
                                <SecondaryButton
                                    type="button"
                                    @click="closeModal()"
                                    class="bg-red-500 hover:bg-red-500 hover:bg-opacity-80"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton
                                    class="ml-2"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Save
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </Modal>
        </section>
    </AuthenticatedLayout>
</template>

<style>
:root {
    --ck-border-radius: 0.375rem !important;
    --dp-border-radius: 0.375rem !important;
    --dp-input-padding: 8px;
}

.ck .ck-focused {
    border: 1px solid #71aac0a6 !important;
}

.ck-editor__editable_inline {
    min-height: 200px;
}

.swal2-default-outline {
    border-radius: 0.375rem !important;
}

.swal2-popup {
    border-radius: 0.575rem !important;
}

input[type="text"]:disabled {
    background-color: rgba(128, 128, 128, 0.096);
}
</style>
