<?php

// This module has no admin-panel routes of its own — the Emergency Access
// admin UI lives in Modules\Admin (EmergencyAccessController, VaultController),
// following the same convention as every other domain module in this app.
// This module only provides the domain model, VaultAccessService, the
// vault-unlock middleware, and the scheduled expiry command.
