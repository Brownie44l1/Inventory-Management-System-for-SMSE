document.addEventListener('DOMContentLoaded', function() {
    // Modal Elements
    const modal = document.getElementById('addAttributeModal');
    const addAttributeButton = document.getElementById('addAttributeButton');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModal = document.getElementById('closeModal');
    
    const editAttributeModal = document.getElementById('editAttributeModal');
    const editCloseModal = document.getElementById('editCloseModal');
    const editCloseModalBtn = document.getElementById('editCloseModalBtn');
    
    const deleteModal = document.getElementById('deleteAttributeModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    let currentAttributeId = null; // Track the current attribute being deleted or edited
    
    // Form Elements
    const attributeForm = document.getElementById('attributeForm');
    const attributeNameInput = document.getElementById('attributeValue');
    const attributeNameError = document.getElementById('attributeValueError');
    const successMessage = document.getElementById('successMessage');
    const closeMsg = document.getElementById('closeMsg');

    // Handle Add Attribute Button click
    addAttributeButton.addEventListener('click', showModal);

    // Close modal on button click
    closeModalBtn.addEventListener('click', hideModal);
    closeModal.addEventListener('click', hideModal);
    closeMsg.addEventListener('click', () => {
        successMessage.style.display = 'none';
    });

    // Function to open the Add Attribute Modal
    function showModal(attributeId) {
        document.getElementById('attributeId').value = attributeId;
        document.getElementById('attributeValueId').value = '';  // Empty for new insertions
        document.getElementById('attributeValue').value = '';
        modal.style.display = 'flex';
    }

    // Function to close the Add Attribute Modal and reset form
    function hideModal() {
        modal.style.display = 'none';
        attributeForm.reset();
        attributeNameError.classList.add('hidden');
    }

    // Handle form submission for Add Attribute (add or edit)
    attributeForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validate the attribute value field
        if (attributeNameInput.value.trim() === '') {
            attributeNameError.classList.remove('hidden');
            return;
        } else {
            attributeNameError.classList.add('hidden');
        }

        const formData = new FormData(attributeForm);
        const attributeId = document.getElementById('attributeId').value;

        if (attributeId !== 'new' && (!attributeId || attributeId <= 0)) {
            alert('Error: Invalid attribute ID.');
            return; // Prevent form submission if ID is invalid for edit operations
        }

        console.log('Attribute ID:', attributeId); 

        fetch(attributeForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log("Raw server response:", text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error('Error parsing JSON:', error);
                throw new Error('Invalid JSON response');
            }
            
            if (data.success) {
                hideModal();
                showSuccessMessage();
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // Handle Edit Button click for all edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Retrieve attribute value data from data attributes
            const attribute = {
                id: this.getAttribute('data-id'),
                value: this.getAttribute('data-value')
            };
            // Open the edit modal with the attribute value data
            openEditModal(attribute);
        });
    });

    // Function to open the Edit Modal and populate fields with attribute value data
    function openEditModal(attributeValueId, attributeId, value) {
        document.getElementById('attributeId').value = attributeId;
        document.getElementById('attributeValueId').value = attributeValueId;
        document.getElementById('attributeValue').value = value;
        modal.style.display = 'flex';
    }

    // Function to close the Edit Attribute Modal
    function closeEditModal() {
        editAttributeModal.style.display = 'none';
    }

    // Event listeners to close the edit modal
    editCloseModal.addEventListener('click', closeEditModal);
    editCloseModalBtn.addEventListener('click', closeEditModal);

    // Handle form validation for Edit Attribute Value Form
    const editAttributeForm = document.getElementById('editAttributeForm');
    editAttributeForm.addEventListener('submit', function(event) {
        const attributeValue = document.getElementById('editAttributeValue').value.trim();
        if (!attributeValue) {
            event.preventDefault();
            document.getElementById('editAttributeValueError').style.display = 'block';
        } else {
            document.getElementById('editAttributeValueError').style.display = 'none';
        }
    });

    // Handle Delete Button click for all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            currentAttributeId = this.getAttribute('data-id');
            deleteModal.style.display = 'flex';
        });
    });

    // Close Delete Modal on button click
    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });
    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    // Handle Confirm Delete action
    confirmDeleteBtn.addEventListener('click', () => {
        if (currentAttributeId) {
            fetch(`attributes_value.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentAttributeId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deleteModal.style.display = 'none';
                    showSuccessMessage();
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    });

    // Function to show success message
    function showSuccessMessage() {
        successMessage.style.display = 'block';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }
});
