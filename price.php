				<?php if ($game['is_free']): ?>
					<span class="free">FREE</span>
				<?php elseif ($game['price_overview']['discount_percent']): ?>
					<div class="discount">
						<?= $game['price_overview']['currency'] ?> <?= number_format($game['price_overview']['final']/100, 2) ?>
						<br><?= $game['price_overview']['discount_percent'] ?>% discount
					</div>
				<?php elseif (isset( $game['price_overview']['initial']) ): ?>
					<?php if ($game['price_overview']['initial'] < 0): ?>
						NOT ON STORE ANYMORE
					<?php elseif ($game['price_overview']['initial']): ?>
						<?= $game['price_overview']['currency'] ?> <?= number_format($game['price_overview']['initial']/100, 2) ?>
					<?php else: ?>
						<span class="free">LIMITED TIME FREE</span>
					<?php endif ?>
				<?php else: ?>
					<span>INCLUDED IN OTHER GAME</span>
				<?php endif ?>