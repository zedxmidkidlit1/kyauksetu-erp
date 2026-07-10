# KAI AI Setup

KAI uses the local deterministic responder by default. Keep this default for normal local development, automated tests, and CI:

```ini
KAI_RESPONDER=local
KAI_PROVIDER_ENABLED=false
```

To enable the external Laravel AI SDK responder in local/dev, set placeholder values like these in `.env`:

```ini
KAI_RESPONDER=external
KAI_PROVIDER_ENABLED=true
KAI_AI_PROVIDER=openai
KAI_AI_MODEL=gpt-4o-mini
KAI_PROVIDER_API_KEY=your-local-provider-key
KAI_PROVIDER_ENDPOINT=
KAI_PROVIDER_TIMEOUT=30
```

Do not commit real API keys. Keep secrets in `.env` or your deployment secret store only.

After changing `.env`, clear cached config:

```bash
vendor/bin/sail artisan config:clear
```

Check configuration without sending a provider request:

```bash
vendor/bin/sail artisan kai:smoke
```

Send one tiny local/dev smoke prompt only after external AI is explicitly enabled and configured:

```bash
vendor/bin/sail artisan kai:smoke --send
```

KAI logs the user and assistant message text plus context keys, driver, provider, model, status, and request ID metadata. Treat chat text as potentially sensitive user content and apply appropriate access, retention, and privacy controls. KAI does not store raw context-builder output, prompt JSON, API keys, or raw provider exception details.

## Current Operational Limitations

- `/api/v1/kai/chat` does not yet have an explicit endpoint-specific rate limit. Add one before beta or production use.
- External provider exceptions currently fall back to the local responder without operational reporting. Add safe error reporting and monitoring before enabling an external provider in production.
- Do not claim external-provider readiness from a configuration-only smoke check. Use Laravel AI fakes in tests and a deliberately approved, minimal live smoke request in a non-production environment.
- Keep provider credentials in deployment secret storage and use production-specific timeout, model, token, and cost limits.
