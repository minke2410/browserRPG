// セクション表示制御
function showSection(sectionId) {
  // セッションに現在のセクションを保存
  const url = new URL(window.location.href);
  url.searchParams.set('section', sectionId);
  window.location.href = url.toString(); // URLを更新してページをリロード
}

// キャラクターの詳細情報をトグル（表示/非表示）する関数
function toggleCharacterDetails(characterId) {
  // すべてのキャラクター詳細を取得
  const allDetails = document.querySelectorAll('.character-details');
  
  // 他のキャラクターの詳細情報を非表示にする
  allDetails.forEach(detail => {
    // 他のキャラクターの詳細が表示されていれば非表示にする
    if (detail.id !== 'details-' + characterId) {
      detail.style.display = 'none';
    }
  });

  // クリックされたキャラクターの詳細をトグル
  const detailsDiv = document.getElementById('details-' + characterId);
  
  // 詳細情報の表示状態を切り替え
  if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
    detailsDiv.style.display = 'block';
  } else {
    detailsDiv.style.display = 'none';
  }
}

// // モーダル表示
// function showModal(message) {
//   const modal = document.getElementById('modal');
//   const modalMessage = document.getElementById('modal-message');
//   modalMessage.textContent = message;  // モーダルにメッセージを表示
//   // modal.style.display = 'block';  // モーダルを表示

//   // 初回のみイベントリスナーを登録
//   // if (!modal.hasAttribute('data-listeners')) {
//     const closeModal = document.getElementById('modal-close');
//     closeModal.addEventListener('click', () => hideModal(modal));

//     window.addEventListener('click', function(event) {
//       if (event.target === modal) {
//         hideModal(modal);
//       }
//     });

//     modal.setAttribute('data-listeners', 'true'); // イベントリスナーが登録済みであることを記録
//   // }

//   // モーダルを表示
//   modal.classList.add('show');



//   // 一定時間後に自動でモーダルを閉じる (例: 3秒)
//   setTimeout(() =>  hideModal(modal), 2000);
// }

// function hideModal(modal) {
//   modal.classList.remove('show'); // 表示クラスを削除

//   // トランジションが終わるタイミングでdisplay: noneを設定
//   setTimeout(() => {
//     modal.style.display = 'none';
//   }, 3000); // CSSのトランジション時間と同じに設定
// }

function showModal() {
  // モーダルの要素を取得
  const modalElement = document.getElementById('myModal');

  // Bootstrapモーダルのインスタンスを作成
  const modal = new bootstrap.Modal(modalElement);

  // モーダルを表示
  modal.show();
}





    
// パーティーがクリックされたときに選択状態を切り替え
function selectParty(partyId) {
    // すでに選択されているパーティーがある場合、その枠を外す
    const previousSelectedParty = document.querySelector('.party-item.selected');
    if (previousSelectedParty) {
        previousSelectedParty.classList.remove('selected');
    }
    
    // 選択されたパーティーを枠で囲む
    const partyItem = document.getElementById('party-' + partyId);
    partyItem.classList.add('selected');
    
    // 変更ボタンを表示
    document.getElementById('change-party-btn').style.display = 'inline-block';
    
    // 選択されたパーティーIDを保存
    window.selectedPartyId = partyId;
}



// レスポンスのJSONをパースする関数
async function parseResponseJson(response) {
  console.log('Response Object:', response); // レスポンスオブジェクト全体を確認

  const text = await response.text();  // レスポンスを文字列として取得
  console.log('Response Text:', text);  // レスポンス内容を確認

  try {
    const result = JSON.parse(text);   // JSONとして解析
    console.log('Response JSON:', result); // 解析した結果を表示
    return result;
  } catch (error) {
    console.log('レスポンスはJSONではありません:', error);
    throw new Error('レスポンスがJSON形式ではありません');
  }
}


// 「パーティーを変更する」ボタンを押したときにサーバーに送信する
async function changeParty() {
  if (!window.selectedPartyId) {
    alert('パーティーが選択されていません。');
    return;
  }
  
  try {
    console.log('Sending fetch request...');
    const response = await fetch('update_active_party.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ party_id: window.selectedPartyId })
    });

    console.log('Response received:', response);

    // レスポンスのJSONをパース
    const result = await parseResponseJson(response);

    if (result.success) {
      showModal('パーティーがアクティブに設定されました！');
      // location.reload(); // 成功したらページをリロード
    } else {
      alert('エラー: ' + result.message);
    }
  } catch (error) {
    console.log('エラーが発生しました:', error);
    alert('通信エラーが発生しました。');
  }
}

