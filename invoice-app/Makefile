up:
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose restart

migrate:
	docker compose run --rm artisan migrate --force

seed:
	docker compose run --rm artisan db:seed --force

fresh:
	docker compose run --rm artisan migrate:fresh --seed --force

logs:
	docker compose logs -f app

bash:
	docker compose exec app bash

tinker:
	docker compose run --rm artisan tinker
