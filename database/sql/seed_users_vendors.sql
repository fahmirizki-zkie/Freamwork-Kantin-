START TRANSACTION;

-- Seed users (email unik, aman jika dijalankan berulang)
INSERT INTO users (
    name,
    email,
    id_google,
    password,
    otp,
    email_verified_at,
    remember_token,
    created_at,
    updated_at
)
VALUES
    (
        'Vendor Google 1',
        'vendor1@example.com',
        'google_111111111111111111111',
        '$2y$12$V0cNho.m8Mvrk9B6mentDOGzYrhjcfhnk/T1m0Ki66aiD9DsKrS7i',
        NULL,
        NOW(),
        NULL,
        NOW(),
        NOW()
    ),
    (
        'Vendor Manual 2',
        'vendor2@example.com',
        NULL,
        '$2y$12$xqpJuCpolJ8vgOJdnomcHuar/2k2iUw3JjbuYIPxef8.VJrkXCY8i',
        NULL,
        NOW(),
        NULL,
        NOW(),
        NOW()
    )
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    id_google = VALUES(id_google),
    updated_at = VALUES(updated_at);

-- Relasi vendor -> user_id berdasarkan email (hindari double insert vendor)
INSERT INTO vendors (user_id, nama_vendor, created_at, updated_at)
SELECT u.id, 'Toko Vendor Satu', NOW(), NOW()
FROM users u
WHERE u.email = 'vendor1@example.com'
AND NOT EXISTS (
    SELECT 1
    FROM vendors v
    WHERE v.user_id = u.id
);

INSERT INTO vendors (user_id, nama_vendor, created_at, updated_at)
SELECT u.id, 'Toko Vendor Dua', NOW(), NOW()
FROM users u
WHERE u.email = 'vendor2@example.com'
AND NOT EXISTS (
    SELECT 1
    FROM vendors v
    WHERE v.user_id = u.id
);

COMMIT;

-- Password plaintext untuk akun contoh:
-- vendor1@example.com -> vendor12345
-- vendor2@example.com -> admin12345
