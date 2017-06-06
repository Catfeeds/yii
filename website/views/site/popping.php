<style>
    .houtai_overlay{
        opacity: 0.3;
        position: fixed;
        bottom: 0;
        top: 0;
        left: 0;
        right: 0;
        background: #000;
        height: 100%;
        width: 100%;
        _position: absolute;
        z-index: 10000;
    }
    .tanchuang {
        width: 90%;
        height: 520px;
        position: fixed;
        z-index: 100000;
        top: 50%;
        margin-top: -250px;
        right: 5%;
        margin-right: -30px;
        border-radius: 4px;
        outline: 0;
        border: 1px solid #666;
        box-shadow: 0 0 10px rgba(0,0,0,.5);
        background: #fafafa;
    }
    .nui-msgbox {
        height: 26px;
    }
    .nui-msgbox-close {
        position: absolute;
        right: 10px;
        top: 5px;
        width: 9px;
        height: 9px;
        cursor: pointer;
        text-decoration: none;
        color: #ccc;
        padding: 3px;
        z-index: 1000000;
    }
    .failCause{
        line-height: 18px;
        display: inline-block;
        height: 120px;
        cursor: text;
        overflow: auto;
        width: 270px;
        resize: none;
        max-width: 270px;
        max-height: 120px;
    }
</style>
<div class="houtai_overlay" style="display: none;"></div>
<div class="tanchuang" style="display: none;">
    <div class="nui-msgbox">
        <a class="nui-msgbox-close" title="关闭"><b>X</b></a>
    </div>
    <div class="showContent">
        
    </div>
    <input type="hidden" id="selKey" name="selKey" value="1">
</div>
<?= $this->context->renderPartial('/jquery/sitePopping') ?>