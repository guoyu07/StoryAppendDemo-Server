<script type="text/ng-template" id="editProductQna.html">
  <div class="edit-section last clearfix">
    <form name="product_qna_form" novalidate>
      <section class="col-xs-18 section-action gutter-padding">
        <div class="row edit-heading">
          <h2>产品FAQ</h2>
        </div>
        <div class="row edit-body">
          <markdown input="qa.md_text" output="qa.md_html" required="false"></markdown>
        </div>
      </section>
    </form>
  </div>
  <button class="col-xs-offset-7 col-xs-4 btn btn-hg btn-primary save-form" data-ng-click="submitChanges()">
    保存
  </button>
</script>