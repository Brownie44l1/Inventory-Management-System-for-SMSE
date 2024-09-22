document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addBrandModal');
    const addBrandButton = document.getElementById('addBrandButton');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const brandForm = document.getElementById('brandForm');
    const brandNameInput = document.getElementById('brandName');
    const brandNameError = document.getElementById('brandNameError');
    const successMessage = document.getElementById('successMessage');
    const closeMsg = document.getElementById('closeMsg');
    const closeModal = document.getElementById('closeModal');
    // Get modal elements
    const editBrandModal = document.getElementById('editBrandModal');
    const editCloseModal = document.getElementById('editCloseModal');
    const editCloseModalBtn = document.getElementById('editCloseModalBtn');
    const editBrandForm = document.getElementById('editBrandForm');

    const deleteModal = document.getElementById('deleteBrandModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let currentBrandId = null;

    // Handle click on delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            currentBrandId = this.getAttribute('data-id'); 
            console.log('Brand ID:', currentBrandId);
            deleteModal.style.display = 'flex'; 
        });
    });

    // Close modal when clicking the close button or cancel button
    closeDeleteModalBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    cancelDeleteBtn.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    // Handle confirm delete
    confirmDeleteBtn.addEventListener('click', () => {
        if (currentBrandId) {
            console.log('Sending Brand ID:', currentBrandId);
            fetch(`brand.php?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentBrandId }), // Send the brand ID to the server
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        deleteModal.style.display = 'none'; // Hide the modal
                        showSuccessMessage(); // Show success message (you can customize this)
                        setTimeout(() => {
                            location.reload(); // Reload the page after a short delay
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
    
    // Function to open the modal and populate fields with brand data
    function openEditModal(brand) {
        // Set form values with brand data
        document.getElementById('brandId').value = brand.id; 
        document.getElementById('editBrandName').value = brand.name;
        document.getElementById('editStatus').value = brand.status;

        // Show the modal
        editBrandModal.style.display = 'flex';
    }

    // Function to close the modal
    function closeEditModal() {
        editBrandModal.style.display = 'none';
    }

    // Event listeners to close the modal
    editCloseModal.addEventListener('click', closeEditModal);
    editCloseModalBtn.addEventListener('click', closeEditModal);

    // Optional: Form submission event
    editBrandForm.addEventListener('submit', function (event) {
        const brandName = document.getElementById('editBrandName').value.trim();
        if (!brandName) {
            event.preventDefault(); 
            document.getElementById('editBrandNameError').style.display = 'block';
        } else {
            document.getElementById('editBrandNameError').style.display = 'none';
        }
    });
    // Add click event listeners to all edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Retrieve brand data from data attributes
            const brand = {
                id: this.getAttribute('data-id'),
                name: this.getAttribute('data-name'),
                status: this.getAttribute('data-status')
            };
            // Open the edit modal with the brand data
            openEditModal(brand);
        });
    });


    function showModal() {
        modal.style.display = 'flex';
    }

    function hideModal() {
        modal.style.display = 'none';
        brandForm.reset();
        brandNameError.classList.add('hidden');
    }

    function showSuccessMessage() {
        successMessage.style.display = 'block';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }

    addBrandButton.addEventListener('click', showModal);
    closeModalBtn.addEventListener('click', hideModal);
    closeModal.addEventListener('click', hideModal);
    closeMsg.addEventListener('click', () => {
        successMessage.style.display = 'none';
    });

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    brandForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Validate the brand name field
        if (brandNameInput.value.trim() === '') {
            brandNameError.classList.remove('hidden');
            return;
        } else {
            brandNameError.classList.add('hidden');
        }

        const formData = new FormData(brandForm);

        fetch(brandForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
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

    function refreshBrandList() {
        // Fetch and update the brand list
    }
});