</div>
</main>
</div><!-- end main wrapper -->
</div><!-- end flex container -->

<script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('d-none');
        }
    }
</script>
</body>

</html>