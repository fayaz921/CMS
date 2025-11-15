<?php
session_start();

// prepare variables for form re-population and errors
$errors = [];
$old = [
    'name'=>'','father_name'=>'','registration_no'=>'','class'=>'','section'=>'',
    'gender'=>'','contact'=>'','address'=>'','email'=>''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // require DB config (adjust path if your config is located elsewhere)
    $dbPath = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/CMS/config/database.php';
    if (!file_exists($dbPath)) {
        // helpful error (do not reveal sensitive info in production)
        die('Missing configuration file: ' . htmlspecialchars($dbPath));
    }
    require_once $dbPath;

    if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
        $errors[] = 'Database connection error.';
    } else {
        // collect & trim inputs
        $fields = array_keys($old);
        foreach ($fields as $f) {
            $old[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
        }

        // validation
        if ($old['name'] === '') $errors[] = 'Student name is required.';
        if ($old['registration_no'] === '') $errors[] = 'Registration number is required.';
        if ($old['class'] === '') $errors[] = 'Class is required.';
        if ($old['section'] === '') $errors[] = 'Section is required.';
        if ($old['gender'] === '') $errors[] = 'Gender is required.';
        if ($old['contact'] === '') $errors[] = 'Contact is required.';
        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

        if (empty($errors)) {
            // prepared insert using variables
            $sql = "INSERT INTO students (name, father_name, registration_no, class, section, gender, contact, address, email)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                $errors[] = 'Prepare failed: ' . $mysqli->error;
            } else {
                $stmt->bind_param(
                    'sssssssss',
                    $old['name'],
                    $old['father_name'],
                    $old['registration_no'],
                    $old['class'],
                    $old['section'],
                    $old['gender'],
                    $old['contact'],
                    $old['address'],
                    $old['email']
                );
                $ok = $stmt->execute();
                if ($ok) {
                    // success: redirect to same page with message (avoid re-post on refresh)
                    header('Location: /CMS/views/authentication/Register.php?success=1');
                    exit;
                } else {
                    $errors[] = 'Insert failed: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Register Page</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Register Page" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant"
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="/CMS/assets/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="/CMS/assets/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="register-page bg-body-secondary">
    <div class="register-box">
      <div class="register-logo">
        <a href="/CMS/index.php"><b>Admin</b>LTE</a>
      </div>
      <div class="card">
        <div class="card-body register-card-body">
          <p class="register-box-msg">Register a new student</p>

<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">Student registered successfully.</div>';
} elseif (!empty($errors)) {
    echo '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($errors as $e) {
        echo '<li>' . htmlspecialchars($e) . '</li>';
    }
    echo '</ul></div>';
}
?>

          <form action="/CMS/views/authentication/Register.php" method="post" novalidate>
            <div class="input-group mb-2">
              <input name="name" type="text" class="form-control" placeholder="Full Name" required value="<?php echo htmlspecialchars($old['name']); ?>" />
              <div class="input-group-text"><span class="bi bi-person"></span></div>
            </div>

            <div class="input-group mb-2">
              <input name="father_name" type="text" class="form-control" placeholder="Father's Name" value="<?php echo htmlspecialchars($old['father_name']); ?>" />
              <div class="input-group-text"><span class="bi bi-person-badge"></span></div>
            </div>

            <div class="input-group mb-2">
              <input name="registration_no" type="text" class="form-control" placeholder="Registration No" required value="<?php echo htmlspecialchars($old['registration_no']); ?>" />
              <div class="input-group-text"><span class="bi bi-hash"></span></div>
            </div>

            <div class="row g-2 mb-2">
              <div class="col">
                <input name="class" type="text" class="form-control" placeholder="Class" required value="<?php echo htmlspecialchars($old['class']); ?>" />
              </div>
              <div class="col">
                <input name="section" type="text" class="form-control" placeholder="Section" required value="<?php echo htmlspecialchars($old['section']); ?>" />
              </div>
            </div>

            <div class="input-group mb-2">
              <select name="gender" class="form-select" required>
                <option value="">Select gender</option>
                <option value="Male" <?php echo ($old['gender']==='Male')?'selected':''; ?>>Male</option>
                <option value="Female" <?php echo ($old['gender']==='Female')?'selected':''; ?>>Female</option>
                <option value="Other" <?php echo ($old['gender']==='Other')?'selected':''; ?>>Other</option>
              </select>
              <div class="input-group-text"><span class="bi bi-gender-ambiguous"></span></div>
            </div>

            <div class="input-group mb-2">
              <input name="contact" type="text" class="form-control" placeholder="Contact" required value="<?php echo htmlspecialchars($old['contact']); ?>" />
              <div class="input-group-text"><span class="bi bi-telephone"></span></div>
            </div>

            <div class="input-group mb-2">
              <input name="email" type="email" class="form-control" placeholder="Email" required value="<?php echo htmlspecialchars($old['email']); ?>" />
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>

            <div class="input-group mb-3">
              <textarea name="address" class="form-control" placeholder="Address" rows="2"><?php echo htmlspecialchars($old['address']); ?></textarea>
            </div>

            <div class="row">
              <div class="col-8">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="agreeTerms" name="agreeTerms" required />
                  <label class="form-check-label" for="agreeTerms">
                    I agree to the <a href="#">terms</a>
                  </label>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Register</button>
                </div>
              </div>
            </div>
          </form>

          <p class="mb-0 mt-3">
            <a href="/CMS/views/authentication/Login.php" class="text-center"> I already have a membership </a>
          </p>
        </div>
      </div>
    </div>

    <!-- scripts -->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="/CMS/assets/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
