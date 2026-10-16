<div>
    <h2>Konten layanan <span class="badge badge-archive">Arsip — tidak tampil di landing</span></h2>
    <p class="form-copy">Atur nama, ringkasan, dan tag layanan dari satu bagian. Tampil di halaman Karya, Tentang Kami, dan subdomain.</p>
    <div class="service-content-grid">
        <div class="service-content-group">
            <h3>Nama layanan</h3>
            <p class="form-copy">Nama tampil di halaman Karya dan Tentang Kami.</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
                <input type="hidden" name="action" value="save_service_names">
                <label for="service-name-tryout">Tryout</label>
                <input id="service-name-tryout" name="service_names[tryout]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['tryout']) ?>" required>
                <label for="service-name-gadai">Gadai</label>
                <input id="service-name-gadai" name="service_names[gadai]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['gadai']) ?>" required>
                <label for="service-name-kitchen">Kitchen</label>
                <input id="service-name-kitchen" name="service_names[kitchen]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['kitchen']) ?>" required>
                <label for="service-name-parkir">Parkir</label>
                <input id="service-name-parkir" name="service_names[parkir]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['parkir']) ?>" required>
                <div class="actions"><button class="primary" type="submit">Simpan nama</button></div>
            </form>
        </div>
        <div class="service-content-group">
            <h3>Alamat layanan</h3>
            <p class="form-copy">Masukkan URL lengkap dengan awalan https://.</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
                <input type="hidden" name="action" value="save_service_urls">
                <label for="service-url-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
                <input id="service-url-tryout" name="service_urls[tryout]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['tryout']) ?>" required>
                <label for="service-url-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
                <input id="service-url-gadai" name="service_urls[gadai]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['gadai']) ?>" required>
                <label for="service-url-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
                <input id="service-url-kitchen" name="service_urls[kitchen]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['kitchen']) ?>" required>
                <div class="actions"><button class="primary" type="submit">Simpan URL</button></div>
            </form>
        </div>
        <div class="service-content-group">
            <h3>Deskripsi</h3>
            <p class="form-copy">Ringkasan untuk halaman Karya dan Tentang Kami.</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
                <input type="hidden" name="action" value="save_service_descriptions">
                <label for="description-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
                <textarea id="description-tryout" name="service_descriptions[tryout]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['tryout']) ?></textarea>
                <label for="description-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
                <textarea id="description-gadai" name="service_descriptions[gadai]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['gadai']) ?></textarea>
                <label for="description-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
                <textarea id="description-kitchen" name="service_descriptions[kitchen]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['kitchen']) ?></textarea>
                <label for="description-parkir"><?= escapeHtml($serviceNames['parkir']) ?></label>
                <textarea id="description-parkir" name="service_descriptions[parkir]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['parkir']) ?></textarea>
                <div class="actions"><button class="primary" type="submit">Simpan deskripsi</button></div>
            </form>
        </div>
        <div class="service-content-group">
            <h3>Tag</h3>
            <p class="form-copy">Satu tag per baris; maksimal 8 tag dan 60 karakter per tag. Kosongkan untuk menyembunyikan chip.</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
                <input type="hidden" name="action" value="save_service_tags">
                <label for="tags-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
                <textarea id="tags-tryout" name="service_tags[tryout]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['tryout'])) ?></textarea>
                <label for="tags-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
                <textarea id="tags-gadai" name="service_tags[gadai]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['gadai'])) ?></textarea>
                <label for="tags-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
                <textarea id="tags-kitchen" name="service_tags[kitchen]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['kitchen'])) ?></textarea>
                <label for="tags-parkir"><?= escapeHtml($serviceNames['parkir']) ?></label>
                <textarea id="tags-parkir" name="service_tags[parkir]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['parkir'])) ?></textarea>
                <div class="actions"><button class="primary" type="submit">Simpan tag</button></div>
            </form>
        </div>
    </div>
</div>