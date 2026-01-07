<div class="container">
<div class="row" style="min-height: 5rem;">
  <div class="col align-self-start"></div>
  <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4 col align-self-center">
    <!--start it-card-->
    <article class="it-card it-card-banner rounded shadow-sm border">
      <h3 class="it-card-title">Accesso Utenza</h3>
      
      <div class="it-card-banner-icon-wrapper">
        <svg class="icon icon-secondary icon-xl" aria-hidden="true">
          <use href="/bootstrap-italia/dist/svg/sprites.svg#it-key"></use>
        </svg>
      </div>
      
      <div class="it-card-body">
        <p class="it-card-subtitle">Accedi al servizio con le credenziali che ti sono state fornite</p>
        
        <br />
        
<form method="post" action="">
    <div class="form-group">
        <label for="login">Email o Codice Personale</label>
        <input type="text" class="form-control" id="login" name="login" required>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control input-password" id="password" name="password" required>
    </div>

    <div class="it-card-footer">
        <button type="submit" class="btn btn-outline-primary">Accedi al Sistema</button>
    </div>
</form>
<?php if (!empty($error)): ?>
<div class="alert alert-danger mt-3" role="alert">
  <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
    </article>
    <!--end it-card-->
  </div>
  <div class="col align-self-end"></div>
</div>
</div>