import Swal from 'sweetalert2';

const DeleteAlert = () => {
    return Swal.fire({
        title: "Estás seguro?",
        text: "No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        focusCancel: true,
        showClass: {
            backdrop: 'bg-gray-900/50 backdrop-blur backdrop-filter opacity-100'
        },
        customClass: {
            confirmButton:
                "ml-2 inline-flex items-center place-content-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150",
            cancelButton:
                "inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150",
        },
        buttonsStyling: false,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, eliminarlo!",
        reverseButtons: true,
    });
};

export default DeleteAlert;