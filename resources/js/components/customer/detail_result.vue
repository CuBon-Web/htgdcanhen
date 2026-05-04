<template>
  <div>
     <div class="row">
        <div class="col-md-12">
           <div class="card card-stats card-raised boxtop">
              <div class="card-body">
                 <div class="row">
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-primary">
                                <i class="fa fa-exclamation" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title">
                                {{ detaiData.no_ans }}
                             </h3>
                             <h6 class="stats-title">Trả Lời Sai</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-success">
                                <i class="fa fa-check" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title">
                                {{ detaiData.yes_ans }}
                             </h3>
                             <h6 class="stats-title">Trả Lời Đúng</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-info">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title">
                                {{ skipques  }}
                             </h3>
                             <h6 class="stats-title">Bỏ Qua</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-info">
                                <i class="fa fa-clock" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title">
                                {{ detaiData.time_minute  }} : {{ detaiData.time_second  }}
                             </h3>
                             <h6 class="stats-title">Thời Gian Làm Bài</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-info">
                                <i class="fa fa-flag" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title">
                                {{ detaiData.yes_ans }}/{{ skipquesall  }}
                             </h3>
                             <h6 class="stats-title">Kết Quả</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-md-2 col-6">
                       <div class="statistics">
                          <div class="info">
                             <div class="icon icon-info">
                                <i class="fa fa-flag" aria-hidden="true"></i>
                             </div>
                             <h3 class="info-title" v-if="detaiData.type == 'fulltest' ">
                                Full Test
                             </h3>
                             <h3 class="info-title" v-if="detaiData.type != 'fulltest' "">
                                Luyện Tập
                             </h3>
                             <h6 class="stats-title">Loại</h6>
                          </div>
                       </div>
                    </div>
                    <div class="col-lg-12">
                       <div class="instructor-profile-right">
                          <div class="inner">
                             <div class="section-title text-start">
                                <h3 class="tit-phantic">Chi Tiết Bài Thi:</h3>
                                <div class="row">
                                   <div class="col-md-6 col-6" :key="indextr" v-for="(item, indextr) in part">
                                      <div >
                                         <h3 class="tit-part">{{item.name}}</h3>
                                         <div class="row" :key="index" v-for="(q, index) in item.questions">
                                          <div class="col-md-6 col-6" >
                                            <div class="result-answers-list">
                                            <div >
                                               <div class="result-answers-item">
                                                  <div class="question-content text-highlightable">
                                                  </div>
                                                  <span class="question-number"><strong>{{q.numerical_order}}</strong></span>
                                                  <div class="question-answers">
                                                     <div v-if="q.name != null" class="question-text form-check">
                                                        <p>{{q.name}}</p>
                                                     </div>
                                                     <div class="form-check">
                                                        <label class="form-check-label" >
                                                        {{q.option_1}}
                                                        </label>
                                                     </div>
                                                     <div class="form-check">
                                                        <label class="form-check-label" >
                                                        {{q.option_2}}
                                                        </label>
                                                     </div>
                                                     <div class="form-check">
                                                        <label class="form-check-label" >
                                                        {{q.option_3}}
                                                        </label>
                                                     </div>
                                                     <div class="form-check">
                                                        <label class="form-check-label">
                                                        {{q.option_4}}
                                                        </label>
                                                     </div>
                                                  </div>
                                                  <span>
                                                  <span class="text-answerkey">{{q.ans_text}}</span> --
                                                  <span v-if="getMyAns(q.id,q.numerical_order) === true">
                                                  <span v-if="compareResult(q.ans,q.numerical_order) === true">
                                                  <span>
                                                  Đáp án học viên:
                                                  <i class="mr-1 text-answerkeyae" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_1'">{{q.option_1}} </i>  
                                                  <i class="mr-1 text-answerkeyae" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_2'">{{q.option_2}} </i>  
                                                  <i class="mr-1 text-answerkeyae" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_3'">{{q.option_3}} </i> 
                                                  <i class="mr-1 text-answerkeyae" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_4'">{{q.option_4}} </i>  
                                                  </span>
                                                  </span>
                                                  <span v-if="compareResult(q.ans,q.numerical_order) === false">
                                                  <span>
                                                  Đáp án học viên:
                                                  <i class="mr-1 text-answerfail" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_1'">{{q.option_1}} </i>  
                                                  <i class="mr-1 text-answerfail" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_2'">{{q.option_2}} </i>  
                                                  <i class="mr-1 text-answerfail" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_3'">{{q.option_3}} </i> 
                                                  <i class="mr-1 text-answerfail" v-if="getMyAnsResult(q.id,q.numerical_order) == 'option_4'">{{q.option_4}} </i>  
                                                  </span>
                                                  </span>
                                                  </span>
                                                  <span v-else >
                                                  <i class="mr-1 chuatraloi">Chưa Trả Lời</i>
                                                  </span>
                                                  </span>
                                               </div>
                                            </div>
                                         </div>
                                          </div>
                                          <div class="col-md-6 col-6" >
                                            <!-- <vs-image :src="`https://picsum.photos/400/400?image=3${index}`" v-for="(image, index) in 9" /> -->
                                            <img :src="q.image" alt="">
                                          </div>
                                         </div>
                                         
                                      </div>
                                   </div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</template>
<script>
  import { mapActions } from "vuex";
    
    export default {
      components: {
      },
      data() {
        return {
          detaiData:{
              id:this.$route.params.id_result,
          },
          part:[],
          allques:0,
          skipques:0,
          skipquesall:0,
          question:{},
          ac:""
        };
      },
      methods: {
      ...mapActions(["detailResult","editQuizExamId",'editQuizQuestionId']),
      getDetailResult(){
          this.loadings == true;
          this.detailResult({id:this.detaiData.id}).then(response => {
              this.loadings == false;
              this.detaiData = response.data;
              this.detaiData.result_json = JSON.parse(response.data.result_json);
              this.part = response.part;
              this.editQuizExamId({id:this.detaiData.exam_id}).then(response => {
                  this.allques = response.data.questions.length;
                  this.skipques = response.data.questions.length - this.detaiData.all_ans;
                  this.skipquesall = response.data.questions.length;
                  // this.list_result = response.data.resulttest;
              }).catch(error => {
                  // this.loadings == false;
              })
              // this.list_result = response.data.resulttest;
          }).catch(error => {
              this.loadings == false;
          })
      },
       getMyAns(quetions_id,numerical_order) {
        var result_json_re = this.detaiData.result_json;
        for (const element of result_json_re) {
          if(element.numerical_order == numerical_order){
            return true;
          }
        }
      },
      compareResult(ans,numerical_order){
        var result_json_re = this.detaiData.result_json;
        for (const element of result_json_re) {
          
          if(element.numerical_order == numerical_order){
            if(element.result == ans){
              return true;
            }else{
              return false;
            }
          }
        }
      },
      getMyAnsResult(quetions_id,numerical_order){
        var result_json_re = this.detaiData.result_json;
        for (const element of result_json_re) {
          
          if(element.numerical_order == numerical_order){
            return element.result;
          }
        }
          
      }
  
      },
      mounted() {
        this.getDetailResult();
      //   this.editQuizExamIds();
    }
    };
    
</script>
<style>
</style>