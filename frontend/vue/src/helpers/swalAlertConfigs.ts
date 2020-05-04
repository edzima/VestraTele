import {SweetAlertOptions} from "sweetalert2";

export const noteDelConfirmSwal: SweetAlertOptions = {
    title: 'Usunąć notatkę?',
    text: 'Ta operacja nie może zostać cofnięta',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Tak usuń!',
    confirmButtonColor: '#95a2a9',
    cancelButtonText: 'Nie, zachowaj!',
    cancelButtonColor: '#0c9cff',
    showCloseButton: true,
};
export const noteEditConfirmSwal: SweetAlertOptions = {
    title: 'Zapisać notatkę?',
    text: 'Ta operacja nie może zostać cofnięta',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Tak zapisz!',
    confirmButtonColor: '#0c9cff',
    cancelButtonText: 'Nie, cofnij!',
    cancelButtonColor: '#95a2a9',
    showCloseButton: true,
};
