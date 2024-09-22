<?php 
session_start();
include '../build/config/db_conn.php';

// Initialize variables
$company = [
    'id' => '',
    'company_name' => '',
    'fee' => '',
    'vat_charge' => '',
    'address' => '',
    'phone' => '',
    'country' => '',
    'message' => ''
];

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = sanitize_input($_POST['company_name'] ?? '');
    $fee = filter_var($_POST['fee'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $vat_charge = filter_var($_POST['vat_charge'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $address = sanitize_input($_POST['address'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $country = sanitize_input($_POST['country'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    $id = filter_var($_POST['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);

    // Validate required fields
    if (!empty($company_name) && !empty($fee) && !empty($vat_charge) && !empty($address) && !empty($phone) && !empty($country)) {
        if ($id) {
            // Update existing company
            $sql = "UPDATE company SET company_name=?, fee=?, vat_charge=?, address=?, phone=?, country=?, message=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsssssi", $company_name, $fee, $vat_charge, $address, $phone, $country, $message, $id);
        } else {
            // Insert new company
            $sql = "INSERT INTO company (company_name, fee, vat_charge, address, phone, country, message) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsssss", $company_name, $fee, $vat_charge, $address, $phone, $country, $message);
        }

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Record saved successfully";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "All fields except Message are required.";
    }
}

// Load existing company data if editing
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT * FROM company WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $company = $stmt->get_result()->fetch_assoc() ?: $company;
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company</title>
    <link rel="stylesheet" href="../../dist/output.css">
    <link rel="stylesheet" href="../build/css/overflow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-white font-sans">
    <div class="flex min-h-screen">
        <div id="sidebar" class="sidebar w-60">
            <?php include '../build/include/sidebar.php'; ?>
        </div>
        <div id="mainContent" class="main-content flex flex-col flex-grow sidebar-expanded">
            <div><?php include '../build/include/header.php'; ?></div>
            <main class="w-full max-w-4xl p-6 mt-3 mx-auto">
                <h1 class="text-xl font-semibold mb-6"><?php echo isset($id) ? 'Edit Company' : 'Manage Company'; ?></h1>
                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">'.$_SESSION['success_message'].'</div>';
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">'.$_SESSION['error_message'].'</div>';
                    unset($_SESSION['error_message']);
                }
                ?>
                <form class="bg-slate-200 px-8 py-4 mb-4" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($company['id']); ?>">
                    <?php
                    $fields = [
                        'company_name' => 'Company name',
                        'fee' => 'Fee',
                        'vat_charge' => 'Vat Charge',
                        'address' => 'Address',
                        'phone' => 'Phone',
                        'country' => 'Country'
                    ];
                    foreach ($fields as $name => $label) {
                        echo "<div class='mb-4'>
                                <label class='block text-gray-700'>{$label}</label>
                                <input type='text' name='{$name}' value='".htmlspecialchars($company[$name])."' class='w-full p-2 border border-gray-300 rounded-md' required>
                              </div>";
                    }
                    ?>
                    <div class="mb-4">
                        <label class="block text-gray-700">Message</label>
                        <textarea name="message" class="w-full p-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($company['message']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Currency</label>
                        <input type="text" name="currency" value="NGN" class="w-full p-2 border border-gray-300 rounded-md" readonly>
                    </div>
                    <div class="flex items-center mt-6 pb-12">
                        <button type="submit" class="bg-blue-500 mr-4 text-white p-2 rounded-md"><?php echo isset($id) ? 'Update Changes' : 'Save Changes'; ?></button>
                    </div>
                </form>
            </main>
            <div class="w-full flex items-center justify-center">
                <?php include '../build/include/footer.php'; ?>
            </div>
        </div>
    </div>
    <script src="../build/javascript/script.js"></script>
</body>
</html>
