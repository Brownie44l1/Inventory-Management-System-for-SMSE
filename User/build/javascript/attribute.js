document.addEventListener('DOMContentLoaded', function () {
    // Elements for adding attributes
    const addAttributeButton = document.getElementById('addAttributeButton');
    const addAttributeModal = document.getElementById('addAttributeModal');
    const closeAddModalBtns = [document.getElementById('closeModal'), document.getElementById('closeModalBtn')];
    const successMessage = document.getElementById('successMessage');
    const attributeForm = document.getElementById('attributeForm');
    const attributeNameError = document.getElementById('attributeNameError');
    const closeMsg = document.getElementById('closeMsg');

    // Elements for editing attributes
    const editAttributeModal = document.getElementById('editAttributeModal');
    const closeEditModalBtns = [document.getElementById('editCloseModal'), document.getElementById('editCloseModalBtn')];
    const editAttributeForm = document.getElementById('editAttributeForm');
    const editAttributeNameError = document.getElementById('editAttributeNameError');

    // Elements for deleting attributes
    const deleteAttributeModal = document.getElementById('deleteAttributeModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');


    let deleteAttributeId = null;
    let isModalOpen = false;

    // Open delete modal 
    function openDeleteModal(attributeId) {
        if (isModalOpen) return; 
        isModalOpen = true;

        deleteAttributeId = attributeId; 
        deleteAttributeModal.style.display = 'flex'; 
        console.log('Opened delete modal for ID:', deleteAttributeId);
    }

    // Attach event listener to delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const attributeId = this.getAttribute('data-id');
            openDeleteModal(attributeId);
        });
    });

    // Close delete modal
    function closeDeleteModal() {
        deleteAttributeModal.style.display = 'none';
        deleteAttributeId = null; 
        isModalOpen = false;
        console.log('Closed delete modal');
    }

    closeDeleteModalBtn.addEventListener('click', closeDeleteModal); 
    cancelDeleteBtn.addEventListener('click', closeDeleteModal); 

    // Confirm delete action
    confirmDeleteBtn.addEventListener('click', function () {
        if (deleteAttributeId !== null) {
            console.log('Confirm button clicked for ID:', deleteAttributeId);
            console.log('Sending delete request for ID:', deleteAttributeId);

            fetch('attributes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'action': 'delete',
                    'id': deleteAttributeId
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server Response:', data);
                if (data.success) {
                    successMessage.style.display = 'block';
                    closeDeleteModal(); 
                    setTimeout(() => {
                        window.location.reload(); 
                    }, 2000);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('An error occurred. Please try again later.');
                closeDeleteModal(); 
            });
        }
    });

    // Open modal when "Add Attribute" button is clicked
    addAttributeButton.addEventListener('click', function () {
        addAttributeModal.style.display = 'flex';
    });

    // Close modals on close buttons
    closeAddModalBtns.forEach(btn => btn.addEventListener('click', function () {
        addAttributeModal.style.display = 'none';
    }));

    // Close success message
    closeMsg.addEventListener('click', function () {
        successMessage.style.display = 'none';
    });

    // Handle form submission for adding attributes
    attributeForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(attributeForm);

        fetch(attributeForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response Data:', data);
            if (data.success) {
                successMessage.style.display = 'block';
                addAttributeModal.style.display = 'none';
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                attributeNameError.textContent = data.message;
                attributeNameError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Open edit attribute modal and populate fields
    function openEditModal(attributeId, attributeName, status) {
        document.getElementById('attributeId').value = attributeId;
        document.getElementById('editAttributeName').value = attributeName;
        document.getElementById('editStatus').value = status;
        editAttributeModal.style.display = 'flex';
    }

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const attributeId = this.getAttribute('data-id');
            const attributeName = this.getAttribute('data-name');
            const status = this.getAttribute('data-status');
            openEditModal(attributeId, attributeName, status);
        });
    });

    // Close edit attribute modal
    closeEditModalBtns.forEach(btn => btn.addEventListener('click', function () {
        editAttributeModal.style.display = 'none';
    }));

    // Handle form submission for editing attributes
    editAttributeForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(editAttributeForm);

        fetch(editAttributeForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successMessage.style.display = 'block';
                editAttributeModal.style.display = 'none';
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                editAttributeNameError.textContent = data.message;
                editAttributeNameError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
