    </main> <footer class="bg-gray-800 text-gray-300 py-8 mt-auto shadow-inner">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">

                <div>
                    <h3 class="text-lg font-semibold text-white mb-3">ABOUT MAWALLET</h3>
                    <p class="text-sm leading-relaxed">
                        MaWallet is your personal finance companion, designed to help you track income, manage expenses, and gain clear insights into your spending habits.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 uppercase">Contact</h3>
                    <p class="text-sm">Bandar Lampung, Indonesia</p>
                    <p class="text-sm">Email: <a href="mailto:support@mawallet.app" class="underline hover:text-emerald-400">support@mawallet.app</a></p>
                    <p class="text-sm">Phone: <a href="tel:+62123456789" class="underline hover:text-emerald-400">+62 123-456-789</a></p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white mb-3 uppercase">Follow Us</h3>
                    <div class="flex justify-center md:justify-start space-x-6">
                        <a href="#" aria-label="Facebook" class="hover:text-emerald-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" aria-label="Twitter" class="hover:text-emerald-400 transition-colors">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-twitter"><path d="M22 4s-.7 2.1-2 3.4c1.6 1.4 3.3 4.4 3.3 4.4s-1.4 1.4-2.8 2.1c.2 2.1 1.4 4.2 1.4 4.2s-2.1 1.4-4.2 1.4c-2.1 0-4.2-1.4-4.2-1.4s.2-2.1 1.4-4.2c-1.4-.7-2.8-2.1-2.8-2.1s1.8-2.9 3.3-4.4c-1.3-1.3-2-3.4-2-3.4s1.4-1.4 4.2-1.4c2.8 0 4.2 1.4 4.2 1.4z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" class="hover:text-emerald-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="my-6 border-gray-700">

            <div class="text-center text-sm">
                <p>Copyright &copy; <?= date('Y'); ?> MaWallet. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();
    </script>

</body>
</html>