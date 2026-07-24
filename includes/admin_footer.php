        <footer style="text-align:center; padding:1.5rem; color:var(--text-light); font-size:0.875rem;">
            &copy; <?php echo date('Y'); ?> Electronic Shop Admin. All rights reserved.
        </footer>
    </div> <!-- End Main Content -->
</div> <!-- End Admin Layout -->
<script>
    // Simple modal logic if needed across pages
    function showModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function hideModal(id) {
        document.getElementById(id).classList.remove('show');
    }
</script>
</body>
</html>
